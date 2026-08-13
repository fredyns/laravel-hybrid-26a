<?php

namespace App\Features\S3\Upload;

use App\Features\S3\BaseS3Controller;
use App\Helpers\S3;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentUploadController extends BaseS3Controller
{
    /**
     * Upload a file to Storage.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx,pptx,xlsx|max:10240', // 10MB max
            'folder' => 'string|nullable', // Optional folder parameter for organized uploads
        ]);

        $file = $request->file('file');
        $folder = trim(S3::tempDir().'/'.$request->input('folder'), '/');

        try {
            $filePath = $this->s3Service->uploadFile($file, $folder);

            if (! $filePath) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to upload file to storage.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'path' => $filePath,
                'folder' => $folder,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'message' => 'File uploaded successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('File upload failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to upload file: '.$e->getMessage(),
            ], 500);
        }
    }
}
