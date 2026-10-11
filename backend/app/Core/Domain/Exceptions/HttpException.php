<?php

declare(strict_types=1);

namespace App\Core\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as SymfonyHttpException;
use Throwable;

class HttpException extends SymfonyHttpException
{
    /**
     * @param  array<string, scalar|array<array-key, mixed>>  $headers
     */
    public function __construct(
        string $message = '',
        public int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?Throwable $previous = null,
        array $headers = [],
        int $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @param  array<string, scalar>  $replace
     */
    protected static function resolveDefaultMessage(string $key, array $replace = []): string
    {
        return __($key, $replace);
    }
}
