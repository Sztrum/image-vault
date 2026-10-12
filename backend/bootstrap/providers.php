<?php

declare(strict_types=1);
use App\Core\CoreModuleServiceProvider;
use App\Modules\MediaLibrary\MediaLibraryModuleServiceProvider;

return [
    CoreModuleServiceProvider::class,
    MediaLibraryModuleServiceProvider::class,
];
