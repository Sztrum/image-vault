<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image;

use App\Core\Application\Providers\ModuleServiceProvider;

class ImageModuleServiceProvider extends ModuleServiceProvider
{
    public const string MODULE_NAME = 'image';

    public function moduleName(): string
    {
        return self::MODULE_NAME;
    }
}
