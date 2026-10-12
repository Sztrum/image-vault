<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary;

use App\Core\Application\Providers\ModuleServiceProvider;
use App\Modules\MediaLibrary\Image\ImageModuleServiceProvider;

class MediaLibraryModuleServiceProvider extends ModuleServiceProvider
{
    public const string MODULE_NAME = 'media-library';

    public function moduleName(): string
    {
        return self::MODULE_NAME;
    }

    public function register(): void
    {
        parent::register();

        $this->app->register(ImageModuleServiceProvider::class);
    }
}
