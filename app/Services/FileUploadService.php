<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload a new file.
     */
    public function upload(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, 'public');
    }

    /**
     * Replace an existing file.
     */
    public function replace(
        UploadedFile $file,
        ?string $oldFile,
        string $folder
    ): string {
        if (
            $oldFile &&
            Storage::disk('public')->exists($oldFile)
        ) {
            Storage::disk('public')->delete($oldFile);
        }

        return $this->upload($file, $folder);
    }

    /**
     * Delete a file.
     */
    public function delete(?string $file): void
    {
        if (
            $file &&
            Storage::disk('public')->exists($file)
        ) {
            Storage::disk('public')->delete($file);
        }
    }
}