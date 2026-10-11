<?php

declare(strict_types=1);

namespace App\Core\Domain\Exceptions;

class QueryHandlerNotRegisteredException extends HttpException
{
    public function __construct(string $queryClass)
    {
        parent::__construct(
            message: self::resolveDefaultMessage('core::domain.query_handler_not_registered', ['queryClass' => $queryClass]),
        );
    }
}
