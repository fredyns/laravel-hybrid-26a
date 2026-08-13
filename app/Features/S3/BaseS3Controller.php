<?php

namespace App\Features\S3;

use App\Http\Controllers\Controller;
use App\Services\S3Service;

abstract class BaseS3Controller extends Controller
{
    protected S3Service $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

}
