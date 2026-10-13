<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Adapters;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Command\CommandInterface;
use App\Core\Application\Command\CommandTransactionalInterface;
use App\Core\Application\Command\Pipes\TransactionPipe;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;

class LaravelCommandBus implements CommandBusInterface
{
    public function __construct(
        private DispatcherContract $bus,
    ) {}

    public function dispatch(CommandInterface $command): void
    {
        $this->bus->pipeThrough(
            $this->commandShouldUseTransaction($command)
                ? [TransactionPipe::class]
                : []
        )->dispatch($command);
    }

    /**
     * @param  array<class-string, class-string>  $map
     */
    public function map(array $map): void
    {
        $this->bus->map($map);
    }

    protected function commandShouldUseTransaction(CommandInterface $command): bool
    {
        return $command instanceof CommandTransactionalInterface;
    }
}
