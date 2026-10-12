<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;

final class InvalidEmailException extends DomainException
{
    public function __construct()
    {
        parent::__construct('core::validation.email');
    }
}
