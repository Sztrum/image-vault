<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObjects;

use App\Shared\Domain\Exceptions\InvalidEmailException;

final readonly class Email
{
    public const int MAX_LENGTH = 255;

    public string $value;

    /**
     * @throws InvalidEmailException
     */
    public function __construct(string $value)
    {
        $value = trim($value);
        throw_if(
            mb_strlen($value) > self::MAX_LENGTH || filter_var($value, FILTER_VALIDATE_EMAIL) === false,
            InvalidEmailException::class,
        );

        $this->value = $value;
    }
}
