<?php

declare(strict_types=1);

namespace Tests\Fixtures\Modules\Library;

use App\Core\Application\Providers\ModuleServiceProvider;

class LibraryModuleServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'library';
    }
}
