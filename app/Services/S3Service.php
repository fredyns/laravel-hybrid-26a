<?php

namespace App\Services;

use App\Helpers\S3;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Service
{
    protected Filesystem $disk;

    protected ?string $targetFolder = null;

    public function __construct()
    {
        $this->disk = Storage::disk();
        $this->targetFolder = 'tmp/'.date('Y/m/d');
    }

    /**
     * Set the target folder for subsequent operations
     *
     * @param  string|null  $folder  The target folder path
     */
    public function setFolder(?string $folder): self
    {
        $this->targetFolder = $folder;

        return $this;
    }

    /**
     * Get the current target folder
     */
    public function getFolder(): ?string
    {
        return $this->targetFolder;
    }

    /**
     * Put content to a file in Storage
     */
    public function put(string $path, string $content): bool
    {
        return $this->disk->put($path, $content);
    }

    /**
     * Upload a file to Storage
     */
    public function uploadFile(UploadedFile $file, string $directory = 'uploads'): string|false
    {
        $filename = S3::sanitizeFileName($file);

        try {
            \Log::info('S3Service upload attempt', [
                'disk' => config('filesystems.default'),
                'directory' => $directory,
                'filename' => $filename,
                'file_exists' => file_exists($file->getRealPath()),
            ]);

            $result = $this->disk->putFileAs($directory, $file, $filename);

            if ($result === false) {
                \Log::error('Storage upload returned false (no exception)', [
                    'directory' => $directory,
                    'filename' => $filename,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('Storage upload failed: '.$e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Upload file content to Storage
     */
    public function uploadContent(string $content, string $path): bool
    {
        try {
            return $this->disk->put($path, $content);
        } catch (\Exception $e) {
            \Log::error('Storage content upload failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get file URL from Storage
     *
     * @param  int  $expiration  (in minutes, default 60)
     */
    public function getFileUrl(string $path, int $expiration = 60): ?string
    {
        try {
            if (! $this->disk->exists($path)) {
                return null;
            }

            return $this->disk->temporaryUrl($path, now()->addMinutes($expiration));
        } catch (\Exception $e) {
            \Log::error('Storage URL generation failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get permanent public URL (if bucket allows public access)
     */
    public function getPublicUrl(string $path): ?string
    {
        try {
            if (! $this->disk->exists($path)) {
                return null;
            }

            return $this->disk->url($path);
        } catch (\Exception $e) {
            \Log::error('Storage public URL generation failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Delete a file from Storage
     */
    public function deleteFile(string $path): bool
    {
        try {
            return $this->disk->delete($path);
        } catch (\Exception $e) {
            \Log::error('Storage delete failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Delete multiple files from Storage
     *
     * @param  array  $filePaths  Array of file paths to delete
     * @return int Number of files successfully deleted
     */
    public function deleteFiles(array $filePaths): int
    {
        $deletedCount = 0;

        foreach ($filePaths as $filePath) {
            // Skip null or empty paths
            if (empty($filePath)) {
                continue;
            }

            // Check if file exists and delete
            if ($this->fileExists($filePath) && $this->deleteFile($filePath)) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Check if file exists in Storage
     */
    public function fileExists(string $path): bool
    {
        try {
            return $this->disk->exists($path);
        } catch (\Exception $e) {
            \Log::error('Storage file check failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get file size
     */
    public function getFileSize(string $path): ?int
    {
        try {
            if (! $this->disk->exists($path)) {
                return null;
            }

            return $this->disk->size($path);
        } catch (\Exception $e) {
            \Log::error('Storage file size check failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get file content
     */
    public function getFileContent(string $path): ?string
    {
        try {
            if (! $this->disk->exists($path)) {
                return null;
            }

            return $this->disk->get($path);
        } catch (\Exception $e) {
            \Log::error('Storage file content retrieval failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * List files in a directory
     */
    public function listFiles(string $directory = ''): array
    {
        try {
            return $this->disk->files($directory);
        } catch (\Exception $e) {
            \Log::error('Storage file listing failed: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Create a directory
     */
    public function createDirectory(string $directory): bool
    {
        try {
            return $this->disk->makeDirectory($directory);
        } catch (\Exception $e) {
            \Log::error('Storage directory creation failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Copy a file within Storage
     */
    public function copyFile(string $from, string $to): bool
    {
        try {
            return $this->disk->copy($from, $to);
        } catch (\Exception $e) {
            \Log::error('Storage file copy failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Move a file within Storage
     */
    public function moveFile(string $from, string $to): bool
    {
        try {
            return $this->disk->move($from, $to);
        } catch (\Exception $e) {
            \Log::error('Storage file move failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get application download URL (serves through application domain)
     *
     * @param  bool  $forceDownload  If true, uses 'force' route to force download
     */
    public function getDownloadUrl(string $path, bool $forceDownload = false): ?string
    {
        try {
            if (! $this->disk->exists($path)) {
                return null;
            }

            // Use 's3.download.inline' for inline view, 's3.download.force' for forced download
            $routeName = $forceDownload ? 's3.download.force' : 's3.download.inline';

            return route($routeName, ['path' => $path]);
        } catch (\Exception $e) {
            \Log::error('Download URL generation failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get MIME type of a file
     */
    public function getMimeType(string $path): ?string
    {
        try {
            if (! $this->disk->exists($path)) {
                return null;
            }

            return $this->disk->mimeType($path);
        } catch (\Exception $e) {
            \Log::error('MIME type retrieval failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Move a file to a target folder and return the new path
     * This is a cleaner alternative to moveToFolder() that doesn't use references
     *
     * @param  string|null  $filePath  The file path to move
     * @param  string|null  $targetFolder  The destination folder path (uses setFolder if null)
     * @return string|null The new file path if successful, null otherwise
     */
    public function moveToFolder(?string $filePath, ?string $targetFolder = null): ?string
    {
        // Skip null or empty paths
        if (empty($filePath)) {
            return null;
        }

        // Use stored target folder if not provided
        $folder = $targetFolder ?? $this->targetFolder;

        if (empty($folder)) {
            \Log::error('Storage moveFileToFolder: No target folder specified');

            return null;
        }

        // Check if file exists
        if (! $this->fileExists($filePath)) {
            \Log::warning("Storage moveFileToFolder: File does not exist: {$filePath}");

            return null;
        }

        // Get the filename from the original path
        $fileName = basename($filePath);

        // Construct the new path
        $newPath = rtrim($folder, '/').'/'.$fileName;

        // Move the file
        if ($this->moveFile($filePath, $newPath)) {
            return $newPath;
        }

        \Log::error("Storage moveFileToFolder: Failed to move file from {$filePath} to {$newPath}");

        return null;
    }
}
