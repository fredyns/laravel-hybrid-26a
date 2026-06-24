<?php

namespace App\Models\Traits;

use App\Helpers\S3;
use Carbon\Carbon;

/**
 * Trait StorageDir
 *
 * @property string $storage_dir
 * @property Carbon $created_at
 */
trait StorageDir
{
    private ?bool $hasStorageDirAttribute;

    public function hasStorageDir(): bool
    {
        if (is_null($this->hasStorageDirAttribute)) {
            $this->hasStorageDirAttribute = $this->getConnection()
                ->getSchemaBuilder()
                ->hasColumn($this->getTable(), 'storage_dir');
        }

        return $this->hasStorageDirAttribute;
    }

    public function storageDir(): string
    {
        if ($this->storage_dir) {
            return $this->storage_dir;
        }

        $createdAt = $this->created_at ?? null;

        if (!$this->hasStorageDir()) {
            return S3::directoryFor(
                $this->getTable(),
                $this->getKey(),
                $createdAt
            );
        }

        return $this->storage_dir = S3::directoryFor(
            $this->getTable(),
            $this->getKey(),
            $createdAt
        );
    }

    /**
     * Get the viewing URL of the file from S3
     */
    public function storageUrl(string $fieldName): ?string
    {
        return empty($this->{$fieldName}) ? null : S3::url($this->{$fieldName});
    }

    /**
     * Get the URL of the file from S3
     */
    public function downloadUrl(string $fieldName): ?string
    {
        return empty($this->{$fieldName}) ? null : S3::downloadUrl($this->{$fieldName});
    }

}
