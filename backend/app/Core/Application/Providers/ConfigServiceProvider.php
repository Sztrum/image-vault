<?php

declare(strict_types=1);

namespace App\Core\Application\Providers;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

abstract class ConfigServiceProvider extends ServiceProvider
{
    protected const string CONFIGS_PATH = '../../Domain/Config';

    public function boot(
        ConfigContract $configRepository
    ): void {
        $this->loadConfigs();
    }

    public function register(): void {}

    private function loadConfigs(): void
    {
        $reflectionClass = new ReflectionClass($this);
        $providerFile = $reflectionClass->getFileName();
        if (!is_string($providerFile)) {
            return;
        }

        $configsPath = implode(DIRECTORY_SEPARATOR, [
            dirname($providerFile),
            static::CONFIGS_PATH,
        ]);

        if (file_exists($configsPath)) {
            $finder = new Finder;
            $configFiles = $finder->files()->in($configsPath)->name('*.php');

            foreach ($configFiles as $configFile) {
                $this->mergeConfigFrom($configFile->getPathname(), $this->getModuleProvider()->moduleName() . '::' . Str::beforeLast($configFile->getFilename(), '.php'));
            }
        }
    }

    public function getModuleProvider(): ModuleServiceProvider
    {
        $routeProviderReflection = new ReflectionClass($this);

        $provider = Collection::make($this->app->getProviders(ModuleServiceProvider::class))
            ->filter(function ($moduleServiceProvider) use ($routeProviderReflection): bool {
                if (!$moduleServiceProvider instanceof ModuleServiceProvider) {
                    return false;
                }

                $reflection = new ReflectionClass($moduleServiceProvider);

                return str_starts_with($routeProviderReflection->getNamespaceName(), $reflection->getNamespaceName());
            })->first();

        if (!$provider instanceof ModuleServiceProvider) {
            throw new \RuntimeException(__('core::domain.config_service_provider_module_provider_not_resolved'));
        }

        return $provider;
    }
}
