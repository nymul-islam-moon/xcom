<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SplFileObject;

class ScanShopsCsvAndQueueChunks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Batchable;

    public $tries = 3;
    public $backoff = [5, 20, 60];

    public function __construct(
        public string $path,
        public array $options = []
    ) {
        $this->onQueue('shops-low');
    }

    public function handle(): void
    {
        $disk       = 'local';
        $chunkSize  = (int)($this->options['chunk_size']   ?? 5000);
        $hasHeader  = (bool)($this->options['header_row']  ?? true);
        $insertMode = (string)($this->options['insert_mode'] ?? 'insert');
        $dateFormat = $this->options['date_format'] ?? null;

        if (!Storage::disk($disk)->exists($this->path)) {
            Log::warning("Import CSV missing: {$this->path}");
            return;
        }

        $fullPath = Storage::disk($disk)->path($this->path);
        $csv = new SplFileObject($fullPath, 'r');
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $header = null;
        $rowCount = 0;

        $batch = Bus::batch([])->name('shops-import: '.$this->path)
            ->allowFailures()->onQueue('shops-low')->dispatch();

        $chunkIdx   = 0;
        $currCount  = 0;
        $writer     = null;
        $chunkPath  = null;

        $openChunk = function() use (&$writer, &$chunkPath, $disk, $chunkIdx) {
            $base   = pathinfo($this->path, PATHINFO_FILENAME);
            $uuid   = bin2hex(random_bytes(8));
            $dir    = 'shops/imports/chunks';
            $chunkPath = "{$dir}/{$base}__{$uuid}__{$chunkIdx}.ndjson";
            Storage::disk($disk)->makeDirectory($dir);
            $writer = Storage::disk($disk)->writeStream($chunkPath);
            if ($writer === false) throw new \RuntimeException("Failed opening writer for {$chunkPath}");
        };

        $closeChunk = function() use (&$writer) {
            if (is_resource($writer)) fclose($writer);
        };

        foreach ($csv as $row) {
            if ($row === [null] || $row === false) continue;

            if ($hasHeader && $header === null) {
                $header = $this->normalizeHeader($row);
                continue;
            }

            $record = $this->mapRowToAssociative($header, $row);
            if ($record === null) continue;

            if (!is_resource($writer)) $openChunk();

            fwrite($writer, json_encode($record, JSON_UNESCAPED_UNICODE) . "\n");

            $currCount++;
            $rowCount++;

            if ($currCount >= $chunkSize) {
                $closeChunk();
                $batch->add(new ProcessShopsChunk(
                    chunkPath: $chunkPath,
                    insertMode: $insertMode,
                    dateFormat: $dateFormat
                ));
                $chunkIdx++;
                $currCount = 0;
                $chunkPath = null;
            }
        }

        if (is_resource($writer)) {
            $closeChunk();
            $batch->add(new ProcessShopsChunk(
                chunkPath: $chunkPath,
                insertMode: $insertMode,
                dateFormat: $dateFormat
            ));
        }

        Log::info("Queued shops import batch (file-backed): rows={$rowCount}, path={$this->path}, batch_id={$batch->id}");
    }

    private function normalizeHeader(array $row): array
    {
        return array_map(function ($h) {
            $h = trim((string)$h);
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h); // strip BOM
            $h = str_replace([' ', '-'], '_', strtolower($h));
            return $h;
        }, $row);
    }

    private function mapRowToAssociative(?array $header, array $row): ?array
    {
        if ($header) {
            $row = array_pad($row, count($header), null);
            return array_combine($header, $row);
        }
        return $row ?: null;
    }
}
