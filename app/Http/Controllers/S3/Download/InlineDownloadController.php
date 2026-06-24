<?php

namespace App\Http\Controllers\S3\Download;

use App\Http\Controllers\S3\BaseS3Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InlineDownloadController extends BaseS3Controller
{
    /**
     * Serve a file INLINE from Storage through the application domain
     *
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $path)
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

        // Determine if file should be displayed inline or downloaded
        $disposition = $request->query('download', false) ? 'attachment' : 'inline';

        // Stream the file from Storage
        return new StreamedResponse(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Length' => $fileSize,
            'Content-Disposition' => $disposition.'; filename="'.$fileName.'"',
            'Cache-Control' => 'public, max-age=3600',
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
