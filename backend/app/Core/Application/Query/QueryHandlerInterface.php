<?php

declare(strict_types=1);

namespace App\Core\Application\Query;

interface QueryHandlerInterface
{
    public function handle(QueryInterface $query): mixed;
}
