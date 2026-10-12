<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;

final class EmptyImageException extends DomainException
{
    public function __construct()
    {
        parent::__construct('image::exceptions.empty');
    }
}
