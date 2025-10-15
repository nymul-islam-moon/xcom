<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessShopsChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable;

    public $tries = 3;

    public $backoff = [5, 20, 60];

    // how many rows we flush to DB per INSERT/UPSERT
    private int $flushSize = 1000;

    public function __construct(
        public string $chunkPath,
        public string $insertMode = 'insert',
        public ?string $dateFormat = null
    ) {
        $this->onQueue('shops-low');
    }

    public function handle(): void
    {
        $disk = 'local';
        if (! Storage::disk($disk)->exists($this->chunkPath)) {
            Log::warning("Chunk missing: {$this->chunkPath}");

            return;
        }

        DB::disableQueryLog();

        $now = Carbon::now();
        $prepared = [];
        $processed = 0;

        $stream = Storage::disk($disk)->readStream($this->chunkPath);
        if ($stream === false) {
            throw new \RuntimeException("Failed opening chunk stream: {$this->chunkPath}");
        }

        while (! feof($stream)) {
            $line = fgets($stream);
            if ($line === false) {
                break;
            }
            $row = json_decode($line, true);
            if (! is_array($row)) {
                continue;
            }

            $email = $this->val($row, 'email');
            $name = $this->val($row, 'name');
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $name) {
                continue;
            }

            $passwordPlain = $this->val($row, 'password') ?: Str::random(12);
            $emailVerified = $this->parseDateNullable($this->val($row, 'email_verified_at'), $this->dateFormat);

            $prepared[] = [
                'name' => $name,
                'email' => strtolower($email),
                'phone' => $this->val($row, 'phone', 15),
                'shop_keeper_name' => $this->val($row, 'shop_keeper_name'),
                'shop_keeper_phone' => $this->val($row, 'shop_keeper_phone'),
                'shop_keeper_nid' => $this->val($row, 'shop_keeper_nid'),
                'shop_keeper_photo' => $this->val($row, 'shop_keeper_photo'),
                'shop_keeper_email' => $this->val($row, 'shop_keeper_email'),
                'shop_keeper_tin' => $this->val($row, 'shop_keeper_tin'),
                'dbid' => $this->val($row, 'dbid'),
                'bank_name' => $this->val($row, 'bank_name'),
                'bank_account_number' => $this->val($row, 'bank_account_number'),
                'bank_branch' => $this->val($row, 'bank_branch'),
                'shop_logo' => $this->val($row, 'shop_logo'),
                'website_url' => $this->val($row, 'website_url'),
                'description' => $this->val($row, 'description'),
                'business_address' => $this->val($row, 'business_address'),
                'email_verified_at' => $emailVerified,
                'password' => Hash::make($passwordPlain),
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($prepared) >= $this->flushSize) {
                $this->flush($prepared);
                $processed += $this->flushSize;
                $prepared = [];
            }
        }
        fclose($stream);

        if (! empty($prepared)) {
            $this->flush($prepared);
            $processed += count($prepared);
        }

        Storage::disk($disk)->delete($this->chunkPath);

        Log::info("Processed chunk {$this->chunkPath}, rows={$processed}, mode={$this->insertMode}");
    }

    private function flush(array $rows): void
    {
        $table = DB::table('shops');

        if ($this->insertMode === 'upsert') {
            $updateCols = [
                'name',
                'phone',
                'shop_keeper_name',
                'shop_keeper_phone',
                'shop_keeper_nid',
                'shop_keeper_photo',
                'shop_keeper_email',
                'shop_keeper_tin',
                'dbid',
                'bank_name',
                'bank_account_number',
                'bank_branch',
                'shop_logo',
                'website_url',
                'description',
                'business_address',
                'email_verified_at',
                'password',
                'remember_token',
                'updated_at',
            ];
            $table->upsert($rows, ['email'], $updateCols);
        } else {
            $table->insertOrIgnore($rows);
        }
    }

    private function val(array $row, string $key, ?int $maxLen = null): ?string
    {
        $v = isset($row[$key]) ? trim((string) $row[$key]) : null;
        if ($maxLen && $v !== null) {
            return mb_substr($v, 0, $maxLen);
        }

        return $v === '' ? null : $v;
    }

    private function parseDateNullable(?string $value, ?string $format): ?string
    {
        if (! $value) {
            return null;
        }
        try {
            return $format
                ? Carbon::createFromFormat($format, $value)->toDateTimeString()
                : Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }
}
