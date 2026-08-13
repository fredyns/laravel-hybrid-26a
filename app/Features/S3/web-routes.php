<?php

use App\Features\S3\Download\ForceDownloadController;
use App\Features\S3\Download\InlineDownloadController;
use App\Features\S3\Upload\DocumentUploadController;
use App\Features\S3\Upload\ImageUploadController;
use Illuminate\Support\Facades\Route;

// S3 Download actions, available for public
Route::prefix('s3/download')
    ->name('s3.download.')
    ->group(function () {

        Route::get('inline/{path}', InlineDownloadController::class)
            ->where('path', '.*')
            ->name('inline');

        Route::get('force/{path}', ForceDownloadController::class)
            ->where('path', '.*')
            ->name('force');
    });

// S3 Upload actions, available for authenticated users only
Route::prefix('s3/upload')
    ->name('s3.upload.')
    ->middleware(['auth:sanctum', 'verified'])
    ->group(function () {
        Route::post('docs', DocumentUploadController::class)->name('docs');
        Route::post('image', ImageUploadController::class)->name('image');
    });
