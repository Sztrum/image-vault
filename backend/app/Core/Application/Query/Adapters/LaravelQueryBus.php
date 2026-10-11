<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Adapters;

use App\Core\Application\Query\QueryBusInterface;
use App\Core\Application\Query\QueryHandlerInterface;
use App\Core\Application\Query\QueryInterface;
use App\Core\Domain\Exceptions\QueryHandlerNotRegisteredException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Throwable;

class LaravelQueryBus implements QueryBusInterface
{
    /** @var array<class-string, class-string> */
    private array $handlers = [];

    public function __construct(
        private Container $container,
    ) {}

    /**
     * @throws BindingResolutionException|QueryHandlerNotRegisteredException|Throwable
     */
    public function ask(QueryInterface $query): mixed
    {
        $handlerClass = $this->handlers[$query::class] ?? null;

        throw_if($handlerClass === null, QueryHandlerNotRegisteredException::class, $query::class);

        $handler = $this->container->make($handlerClass);

        throw_if(!$handler instanceof QueryHandlerInterface, QueryHandlerNotRegisteredException::class, $query::class);

        return $handler->handle($query);
    }

    /**
     * @param  array<class-string, class-string>  $map
     */
    public function map(array $map): void
    {
        $this->handlers = array_merge($this->handlers, $map);
    }
}
