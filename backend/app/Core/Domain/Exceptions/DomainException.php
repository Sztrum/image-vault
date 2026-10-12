<?php

declare(strict_types=1);

namespace App\Core\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DomainException extends HttpException
{
    /**
     * Translation key used when no message is given. Subclasses override only this constant.
     */
    protected const string DEFAULT_MESSAGE_KEY = '';

    /**
     * @param  array<string, scalar>  $replace
     * @param  array<string, string>  $headers
     */
    public function __construct(
        ?string $message = null,
        array $replace = [],
        ?Throwable $previous = null,
        array $headers = [],
        int $code = 0,
    ) {
        $message ??= static::DEFAULT_MESSAGE_KEY;

        parent::__construct(
            message: $message !== '' ? __($message, $replace) : '',
            statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            previous: $previous,
            headers: $headers,
            code: $code
        );
    }
}
