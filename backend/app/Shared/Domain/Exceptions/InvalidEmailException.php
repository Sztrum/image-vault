<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;

final class InvalidEmailException extends DomainException
{
    /**
     * @see backend/app/Core/Application/Translations/pl/validation.php
     */
    protected const string DEFAULT_MESSAGE_KEY = 'core::validation.email';
}
