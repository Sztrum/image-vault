<?php

declare(strict_types=1);

namespace App\Core\Application\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class ServiceProvider extends BaseServiceProvider
{
    public function register(): void {}
}
