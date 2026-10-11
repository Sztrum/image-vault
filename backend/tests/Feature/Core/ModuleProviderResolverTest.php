<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Application\Providers\Resolvers\ModuleProviderResolver;
use App\Core\Domain\Exceptions\ModuleProviderNotResolvedException;
use Tests\Fixtures\Modules\Library\Book\Application\Providers\BookOwnedClass;
use Tests\Fixtures\Modules\Library\Book\BookModuleServiceProvider;
use Tests\Fixtures\Modules\Library\LibraryModuleServiceProvider;
use Tests\Fixtures\Modules\Library\LibraryOwnedClass;
use Tests\Fixtures\Modules\LibraryTag\LibraryTagModuleServiceProvider;
use Tests\Fixtures\Modules\LibraryTag\LibraryTagOwnedClass;
use Tests\Fixtures\Outside\OutsideClass;
use Tests\TestCase;

class ModuleProviderResolverTest extends TestCase
{
    private ModuleProviderResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        // Parent module registered before its nested module: the resolver must not depend on this order.
        $this->app->register(LibraryModuleServiceProvider::class);
        $this->app->register(BookModuleServiceProvider::class);
        $this->app->register(LibraryTagModuleServiceProvider::class);

        $this->resolver = $this->app->make(ModuleProviderResolver::class);
    }

    public function test_it_resolves_the_most_specific_nested_module(): void
    {
        $this->assertSame('book', $this->resolver->resolveFor(new BookOwnedClass)->moduleName());
    }

    public function test_it_resolves_the_parent_module_for_its_own_classes(): void
    {
        $this->assertSame('library', $this->resolver->resolveFor(new LibraryOwnedClass)->moduleName());
    }

    public function test_it_does_not_match_module_with_common_name_prefix(): void
    {
        $this->assertSame('library-tag', $this->resolver->resolveFor(new LibraryTagOwnedClass)->moduleName());
    }

    public function test_it_throws_when_class_belongs_to_no_module(): void
    {
        $this->expectException(ModuleProviderNotResolvedException::class);

        $this->resolver->resolveFor(new OutsideClass);
    }
}
