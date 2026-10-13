<?php

declare(strict_types=1);

namespace App\Core\Application\Query;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;

    /**
     * @param  array<class-string, class-string>  $map
     */
    public function map(array $map): void;
}
