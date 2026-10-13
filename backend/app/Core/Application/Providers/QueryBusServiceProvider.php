<?php

declare(strict_types=1);

namespace App\Core\Application\Providers;

use App\Core\Application\Query\QueryBusInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class QueryBusServiceProvider extends BaseServiceProvider
{
    /** @var array<class-string, class-string> */
    protected array $queries = [];

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $queryBus = $this->app->make(QueryBusInterface::class);
        $queryBus->map($this->queries);

        $this->registerQueries($queryBus);
    }

    protected function registerQueries(QueryBusInterface $queryBus): void {}
}
