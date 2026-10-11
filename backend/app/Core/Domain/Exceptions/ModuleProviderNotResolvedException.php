<?php

declare(strict_types=1);

namespace App\Core\Domain\Exceptions;

class ModuleProviderNotResolvedException extends HttpException
{
    public function __construct(string $ownerClass)
    {
        parent::__construct(
            message: self::resolveDefaultMessage('core::domain.module_provider_not_resolved', ['ownerClass' => $ownerClass]),
        );
    }
}
