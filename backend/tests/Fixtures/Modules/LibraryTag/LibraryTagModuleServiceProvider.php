<?php

declare(strict_types=1);

namespace Tests\Fixtures\Modules\LibraryTag;

use App\Core\Application\Providers\ModuleServiceProvider;

class LibraryTagModuleServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'library-tag';
    }
}
