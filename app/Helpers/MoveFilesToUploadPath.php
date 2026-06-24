<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MoveFilesToUploadPath
{
    /**
     * Handle moving uploaded files to their final destination.
     *
     * @param  Model  $model  The model instance
     * @param  array  $fields  Array of field names that may contain files
     */
    public function handle(Model $model, array $fields): void
    {
        foreach ($fields as $field) {
            if (isset($model->{$field}) && $model->{$field} instanceof UploadedFile) {
                $this->moveFile($model, $field);
            }
        }
    }

    /**
     * Move a single uploaded file to the upload path.
     *
     * @param  Model  $model  The model instance
     * @param  string  $field  The field name containing the file
     */
    protected function moveFile(Model $model, string $field): void
    {
        /** @var UploadedFile $file */
        $file = $model->{$field};

        if (! $file instanceof UploadedFile) {
            return;
        }

        // Generate a unique filename
        $filename = S3::sanitizeFileName($file);

        // Store the file in the upload path
        $path = $file->storeAs(
            $model->storage_dir,
            $filename,
            'public'
        );

        // Update the model attribute with the stored path
        $model->{$field} = $path;
    }
}
