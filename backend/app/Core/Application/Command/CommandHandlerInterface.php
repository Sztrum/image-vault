<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
