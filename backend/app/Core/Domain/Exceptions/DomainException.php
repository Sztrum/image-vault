<?php

declare(strict_types=1);

namespace App\Core\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DomainException extends HttpException
{
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
        parent::__construct(
            message: $message ? __($message, $replace) : '',
            statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            previous: $previous,
            headers: $headers,
            code: $code
        );
    }
}
