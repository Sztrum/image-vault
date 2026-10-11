<?php

declare(strict_types=1);

namespace App\Core\Application\Providers\Resolvers;

use App\Core\Application\Providers\ModuleServiceProvider;
use App\Core\Domain\Exceptions\ModuleProviderNotResolvedException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use ReflectionClass;
use Throwable;

/**
 * Finds the module that owns a given class, based on namespaces.
 * For nested modules (MediaLibrary\Image) the most specific module wins.
 */
class ModuleProviderResolver
{
    public function __construct(
        private Application $app,
    ) {}

    /**
     * @throws ModuleProviderNotResolvedException|Throwable
     */
    public function resolveFor(object $ownedObject): ModuleServiceProvider
    {
        $ownedNamespace = (new ReflectionClass($ownedObject))->getNamespaceName();

        /** @var Collection<int, ModuleServiceProvider> $allModuleProviders */
        $allModuleProviders = Collection::make($this->app->getProviders(ModuleServiceProvider::class))
            ->filter(static fn (mixed $provider): bool => $provider instanceof ModuleServiceProvider);

        $moduleProvider = $allModuleProviders
            ->filter(fn (ModuleServiceProvider $provider): bool => $this->belongsToModule($ownedNamespace, $provider))
            ->sortByDesc(fn (ModuleServiceProvider $provider): int => strlen($this->getNamespace($provider)))
            ->first();

        throw_if($moduleProvider === null, ModuleProviderNotResolvedException::class, $ownedObject::class);

        return $moduleProvider;
    }

    private function belongsToModule(string $ownedNamespace, ModuleServiceProvider $provider): bool
    {
        $moduleNamespace = $this->getNamespace($provider);

        return $ownedNamespace === $moduleNamespace
            || str_starts_with($ownedNamespace, $moduleNamespace . '\\');
    }

    private function getNamespace(ModuleServiceProvider $provider): string
    {
        return (new ReflectionClass($provider))->getNamespaceName();
    }
}
