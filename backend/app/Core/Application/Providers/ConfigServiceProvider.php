<?php

declare(strict_types=1);

namespace App\Core\Application\Providers;

use App\Core\Application\Providers\Resolvers\ModuleProviderResolver;
use App\Core\Domain\Exceptions\ModuleProviderNotResolvedException;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Throwable;

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

    /**
     * @throws ModuleProviderNotResolvedException|Throwable
     */
    public function getModuleProvider(): ModuleServiceProvider
    {
        return $this->app->make(ModuleProviderResolver::class)->resolveFor($this);
    }
}
