<?php

declare(strict_types=1);

namespace App\Core\Application\Providers;

use App\Core\Application\Command\Adapters\LaravelCommandBus;
use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Validation\Validator;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            abstract: CommandBusInterface::class,
            concrete: fn (Application $app) => new LaravelCommandBus(
                $app->make(DispatcherContract::class),
            )
        );
    }

    public function boot(): void
    {
        $this->app->extend('validator', function (ValidationFactory $factory) {
            $factory->resolver(
                fn (
                    Translator $translator,
                    array $data,
                    array $rules,
                    array $messages,
                    array $customAttributes
                ) => new Validator($translator, $data, $rules, $messages, $customAttributes)
            );

            return $factory;
        });
    }
}
