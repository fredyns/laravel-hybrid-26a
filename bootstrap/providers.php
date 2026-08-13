<?php

use App\Features\FeatureServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FeatureServiceProvider::class,
    FortifyServiceProvider::class,
];
