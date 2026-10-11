<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Abstracts;

use App\Core\Application\Query\QueryHandlerInterface;
use App\Core\Application\Query\QueryInterface;

abstract readonly class BaseQueryHandler implements QueryHandlerInterface
{
    abstract public function handle(QueryInterface $query): mixed;
}
