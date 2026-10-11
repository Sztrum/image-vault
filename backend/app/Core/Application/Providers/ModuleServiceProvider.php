<?php

declare(strict_types=1);

namespace App\Core\Application\Providers;

use InvalidArgumentException;
use ReflectionClass;

abstract class ModuleServiceProvider extends ServiceProvider
{
    protected const TRANSLATIONS_PATH = 'Application/Translations';

    public const TRANSLATIONS_MODULE_SEPARATOR = '::';

    /** @var array<int, array{name: string, path: string}> */
    private static array $registeredModules = [];

    abstract public function moduleName(): string;

    public function register(): void
    {
        $modulePath = $this->getModulePath();
        $this->checkModuleName($this->moduleName(), $modulePath);

        self::$registeredModules[] = [
            'name' => $this->moduleName(),
            'path' => $modulePath,
        ];

        $this->loadTranslations();
    }

    private function checkModuleName(string $moduleName, string $modulePath): void
    {
        foreach (self::$registeredModules as $registeredModule) {
            if ($registeredModule['name'] === $moduleName && $registeredModule['path'] !== $modulePath) {
                throw new InvalidArgumentException(
                    __('core::domain.module_name_registered_with_different_path', [
                        'moduleName' => $moduleName,
                        'firstPath' => $registeredModule['path'],
                        'secondPath' => $modulePath,
                    ])
                );
            }
        }
    }

    private function loadTranslations(): void
    {
        $reflectionClass = new ReflectionClass($this);
        $providerFile = $reflectionClass->getFileName();
        if (!is_string($providerFile)) {
            return;
        }

        $translationsPath = implode(DIRECTORY_SEPARATOR, [
            dirname($providerFile),
            static::TRANSLATIONS_PATH,
        ]);

        if (file_exists($translationsPath)) {
            $this->loadTranslationsFrom($translationsPath, $this->moduleName());
        }
    }

    private function getModulePath(): string
    {
        $reflectionClass = new ReflectionClass($this);

        $providerFile = $reflectionClass->getFileName();
        if (!is_string($providerFile)) {
            throw new \RuntimeException(__('core::domain.module_provider_path_not_resolved'));
        }

        return dirname($providerFile);
    }
}
