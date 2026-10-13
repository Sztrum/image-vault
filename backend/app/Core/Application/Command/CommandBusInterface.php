<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): void;

    /**
     * @param  array<class-string, class-string>  $map
     */
    public function map(array $map): void;
}
