<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

abstract class S3
{
    /**
     * Generate an upload path for a given resource type and ID.
     *
     * @param  string  $resourceType  The type of resource (e.g., 'sample_items')
     * @param  string  $id  The resource ID
     * @param  Carbon|null  $createdAt  The creation date of the resource
     * @return string The generated upload path
     */
    public static function directoryFor(string $resourceType, string $id, ?Carbon $createdAt): string
    {
        $createdAt = $createdAt ?? now();
        $date = $createdAt->format('Y/m/d');

        return "{$resourceType}/{$date}/{$id}";
    }

    /**
     * Generate a temporary upload path for file uploads.
     *
     * @param  string  $resourceType  The type of resource
     * @return string The temporary upload path
     */
    public static function tempDir(string $resourceType = ''): string
    {
        $date = now()->format('Y/m/d');

        return trim("tmp/{$date}/{$resourceType}", '/');
    }

    public static function sanitizeFileName(UploadedFile $file): ?string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($originalName.'-'.Str::random(5));

        return $slug.'.'.$file->getClientOriginalExtension();
    }

    /**
     * Get URL for inline viewing (images, PDFs, etc.)
     *
     * @param  string|null  $path  The file path in S3
     * @return string|null The URL to view the file inline
     */
    public static function url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return route('s3.download.inline', ['path' => $path]);
    }

    /**
     * Get URL for forced download
     *
     * @param  string|null  $path  The file path in S3
     * @return string|null The URL to download the file
     */
    public static function downloadUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return route('s3.download.force', ['path' => $path]);
    }
}
