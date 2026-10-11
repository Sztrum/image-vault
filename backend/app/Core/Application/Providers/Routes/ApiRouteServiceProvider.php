<?php

declare(strict_types=1);

namespace App\Core\Application\Providers\Routes;

use App\Core\Application\Providers\ModuleServiceProvider;
use App\Core\Application\Providers\Resolvers\ModuleProviderResolver;
use App\Core\Domain\Exceptions\ModuleProviderNotResolvedException;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Str;
use Throwable;

abstract class ApiRouteServiceProvider extends RouteServiceProvider
{
    public const VERSION = 'v1';

    public const PREFIX = 'api';

    protected const PREFIX_PATTERN = '%s/%s';

    protected const PATTERN = '%s';

    protected bool $pluralPrefix = true;

    protected bool $prefix = true;

    protected bool $pluralRouteName = true;

    protected string $prefixRouteName = 'api.';

    /**
     * @throws ModuleProviderNotResolvedException|Throwable
     */
    public function getModuleProvider(): ModuleServiceProvider
    {
        return $this->app->make(ModuleProviderResolver::class)->resolveFor($this);
    }

    public static function getRoutePrefix(): string
    {
        return implode('/', [self::PREFIX, static::VERSION]);
    }

    public static function getBaseUrl(): string
    {
        return implode('/', [config('app.url'), trim(static::getRoutePrefix(), '/')]);
    }

    public function getModulePrefix(): string
    {
        $moduleName = $this->getModuleProvider()
            ->moduleName();

        return $this->pluralPrefix ? Str::plural($moduleName) : $moduleName;
    }

    public function getModuleRouteName(): string
    {
        $moduleName = $this->getModuleProvider()
            ->moduleName();

        return $this->pluralRouteName ? Str::plural($moduleName) : $moduleName;
    }

    public function getPrefix(): string
    {
        return $this->hasPrefix()
            ? static::getRoutePrefix() . '/' . $this->getModulePrefix()
            : static::getRoutePrefix();
    }

    public function map(Registrar $router): void
    {
        $router->group([
            'middleware' => $this->middlewares(),
            'prefix' => $this->getPrefix(),
            'as' => $this->prefixRouteName !== '' ? $this->prefixRouteName . $this->getModuleRouteName() . '.' : '',
        ], fn () => $this->registerRoutes($router));
    }

    abstract protected function registerRoutes(Registrar $router): void;

    /**
     * @return list<string>
     */
    protected function middlewares(): array
    {
        return ['api'];
    }

    final protected function hasPrefix(): bool
    {
        return $this->prefix;
    }
}
