<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): void;

    public function dispatchMany(CommandInterface ...$commands): void;

    public function dispatchManyWithTransaction(CommandInterface ...$commands): void;

    /**
     * @param  array<class-string, class-string>  $map
     */
    public function map(array $map): void;
}
