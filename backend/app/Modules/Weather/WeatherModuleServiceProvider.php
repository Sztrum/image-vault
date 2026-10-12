<?php

declare(strict_types=1);

namespace App\Modules\Weather;

use App\Core\Application\Providers\ModuleServiceProvider;
use App\Modules\Weather\Application\Providers\WeatherBindingsServiceProvider;
use App\Modules\Weather\Application\Providers\WeatherConfigServiceProvider;

class WeatherModuleServiceProvider extends ModuleServiceProvider
{
    public const string MODULE_NAME = 'weather';

    public function moduleName(): string
    {
        return self::MODULE_NAME;
    }

    public function register(): void
    {
        parent::register();

        $this->app->register(WeatherConfigServiceProvider::class);
        $this->app->register(WeatherBindingsServiceProvider::class);
    }
}
