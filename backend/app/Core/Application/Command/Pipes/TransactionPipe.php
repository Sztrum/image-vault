<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Pipes;

use Illuminate\Database\ConnectionResolverInterface;
use Throwable;

class TransactionPipe
{
    public function __construct(
        private ConnectionResolverInterface $connectionResolver
    ) {}

    /**
     * @param  callable(mixed): mixed  $next
     *
     * @throws Throwable
     */
    public function handle(mixed $job, callable $next): mixed
    {
        return $this->connectionResolver->connection()
            ->transaction(fn () => $next($job));
    }
}
