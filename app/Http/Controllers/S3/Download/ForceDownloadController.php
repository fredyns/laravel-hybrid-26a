<?php

namespace App\Http\Controllers\S3\Download;

use App\Http\Controllers\S3\BaseS3Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ForceDownloadController extends BaseS3Controller
{
    /**
     * FORCE download a file from STORAGE
     *
     * @return StreamedResponse
     */
    public function __invoke(string $path)
    {
        // Decode the path in case it contains encoded characters
        $path = urldecode($path);

        // Check if file exists
        if (! $this->s3Service->fileExists($path)) {
            abort(404, 'File not found');
        }

        // Get file metadata
        $disk = Storage::disk();
        $mimeType = $disk->mimeType($path);
        $fileSize = $this->s3Service->getFileSize($path);
        $fileName = basename($path);

        // Force download
        return new StreamedResponse(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}
