<?php

declare(strict_types=1);
use App\Core\CoreModuleServiceProvider;
use App\Modules\MediaLibrary\MediaLibraryModuleServiceProvider;
use App\Modules\Weather\WeatherModuleServiceProvider;

return [
    CoreModuleServiceProvider::class,
    MediaLibraryModuleServiceProvider::class,
    WeatherModuleServiceProvider::class,
];
