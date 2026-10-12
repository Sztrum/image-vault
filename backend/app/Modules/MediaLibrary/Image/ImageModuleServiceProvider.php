<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image;

use App\Core\Application\Providers\ModuleServiceProvider;
use App\Modules\MediaLibrary\Image\Application\Providers\ImageCommandBusServiceProvider;
use App\Modules\MediaLibrary\Image\Application\Providers\ImageConfigServiceProvider;
use App\Modules\MediaLibrary\Image\Application\Providers\ImageRouteServiceProvider;

class ImageModuleServiceProvider extends ModuleServiceProvider
{
    public const string MODULE_NAME = 'image';

    public function moduleName(): string
    {
        return self::MODULE_NAME;
    }

    public function register(): void
    {
        parent::register();

        $this->app->register(ImageConfigServiceProvider::class);
        $this->app->register(ImageRouteServiceProvider::class);
        $this->app->register(ImageCommandBusServiceProvider::class);
    }
}
