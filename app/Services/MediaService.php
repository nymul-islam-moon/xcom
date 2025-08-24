<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    /** Store a file and return its path. */
    public function storeFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }

    /** Delete a file if it exists. */
    public function deleteFile(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Store a file from a request input if present; return path or null.
     */
    public function storeFromRequest(Request $request, string $input, string $directory = 'uploads', string $disk = 'public'): ?string
    {
        return $request->hasFile($input)
            ? $this->storeFile($request->file($input), $directory, $disk)
            : null;
    }

    /**
     * If request has a new file in $input: store it, delete $oldPath, return new path.
     * If no new file provided, return null (meaning: keep old path).
     */
    public function replaceFromRequest(Request $request, string $input, ?string $oldPath, string $directory = 'uploads', string $disk = 'public'): ?string
    {
        if (! $request->hasFile($input)) {
            return null; // no change
        }

        $newPath = $this->storeFile($request->file($input), $directory, $disk);
        $this->deleteFile($oldPath, $disk);

        return $newPath;
    }
}
