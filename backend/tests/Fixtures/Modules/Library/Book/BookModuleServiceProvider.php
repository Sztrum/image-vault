<?php

declare(strict_types=1);

namespace Tests\Fixtures\Modules\Library\Book;

use App\Core\Application\Providers\ModuleServiceProvider;

class BookModuleServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'book';
    }
}
