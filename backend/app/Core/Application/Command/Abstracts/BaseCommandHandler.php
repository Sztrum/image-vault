<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Abstracts;

use App\Core\Application\Command\CommandHandlerInterface;
use App\Core\Application\Command\CommandInterface;

abstract readonly class BaseCommandHandler implements CommandHandlerInterface
{
    abstract public function handle(CommandInterface $command): void;
}
