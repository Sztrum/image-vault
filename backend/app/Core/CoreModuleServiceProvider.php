<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Application\Providers\Core\CoreRouteServiceProvider;
use App\Core\Application\Providers\CoreServiceProvider;
use App\Core\Application\Providers\ModuleServiceProvider;

class CoreModuleServiceProvider extends ModuleServiceProvider
{
    public const string MODULE_NAME = 'core';

    public function moduleName(): string
    {
        return self::MODULE_NAME;
    }

    public function register(): void
    {
        parent::register();

        $this->app->register(CoreServiceProvider::class);
        $this->app->register(CoreRouteServiceProvider::class);
    }
}
