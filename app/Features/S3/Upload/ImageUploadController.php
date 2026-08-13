<?php

namespace App\Features\S3\Upload;

use App\Features\S3\BaseS3Controller;
use App\Helpers\ImageOptimizer;
use App\Helpers\S3;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageUploadController extends BaseS3Controller
{
    /**
     * Upload an image to Storage.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,svg,gif,webp|max:5120', // 5MB max
            'folder' => 'string|nullable', // Optional folder parameter for organized uploads
            'optimize' => 'boolean', // Optional optimization flag (default: true)
        ]);

        $file = $request->file('file');
        $folder = trim(S3::tempDir().'/'.$request->input('folder'), '/');
        $shouldOptimize = $request->input('optimize', true);

        try {
            $filePath = null;
            $optimizationInfo = null;

            // Check if we should optimize and if the file can be optimized
            if ($shouldOptimize && ImageOptimizer::canOptimize($file)) {
                try {
                    // First upload the original file to get its path
                    $originalPath = $this->s3Service->uploadFile($file, $folder);

                    if (! $originalPath) {
                        throw new \Exception('Failed to upload original image to storage.');
                    }

                    // Optimize and convert to WebP
                    $optimizedPath = ImageOptimizer::optimizeAndConvert($file, $originalPath);

                    // Upload the optimized WebP version
                    $optimizedFile = new \Illuminate\Http\File(storage_path('app/'.$optimizedPath));
                    $finalPath = $this->s3Service->uploadFile($optimizedFile, $folder);

                    if ($finalPath) {
                        // Use the optimized version as the final path
                        $filePath = $finalPath;

                        // Get optimization statistics
                        $optimizationInfo = ImageOptimizer::getOptimizationInfo($originalPath, $optimizedPath);

                        // Clean up temporary files
                        \Storage::delete($originalPath);
                        \Storage::delete($optimizedPath);
                    } else {
                        // Fallback to original if optimization upload fails
                        $filePath = $originalPath;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Image optimization failed, using original: '.$e->getMessage());

                    // Fallback to uploading original file without optimization
                    $filePath = $this->s3Service->uploadFile($file, $folder);
                }
            } else {
                // Upload without optimization
                $filePath = $this->s3Service->uploadFile($file, $folder);
            }

            if (! $filePath) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to upload image to storage.',
                ], 500);
            }

            // Get image dimensions if possible
            $dimensions = null;
            if (function_exists('getimagesize')) {
                $imageInfo = getimagesize($file->getPathname());
                if ($imageInfo) {
                    $dimensions = [
                        'width' => $imageInfo[0],
                        'height' => $imageInfo[1],
                    ];
                }
            }

            $response = [
                'success' => true,
                'path' => $filePath,
                'folder' => $folder,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'dimensions' => $dimensions,
                'message' => 'Image uploaded successfully.',
            ];

            // Add optimization info if available
            if ($optimizationInfo) {
                $response['optimization'] = $optimizationInfo;
                $response['message'] .= sprintf(
                    ' Optimized to WebP format with %s%% size reduction.',
                    $optimizationInfo['savings_percent']
                );
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Image upload failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to upload image: '.$e->getMessage(),
            ], 500);
        }
    }
}
