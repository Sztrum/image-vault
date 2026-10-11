<?php

declare(strict_types=1);

namespace App\Core\Application\Providers;

use App\Core\Application\Command\CommandBusInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class CommandBusServiceProvider extends BaseServiceProvider
{
    /** @var array<class-string, class-string> */
    protected array $commands = [];

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $commandBus = $this->app->make(CommandBusInterface::class);
        $commandBus->map($this->commands);

        $this->registerCommands($commandBus);
    }

    protected function registerCommands(CommandBusInterface $commandBus): void {}
}
