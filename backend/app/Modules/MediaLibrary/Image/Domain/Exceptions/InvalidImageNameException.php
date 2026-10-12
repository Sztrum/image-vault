<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageName;

final class InvalidImageNameException extends DomainException
{
    public function __construct()
    {
        parent::__construct('image::exceptions.invalid_name', [
            'max' => ImageName::MAX_LENGTH,
        ]);
    }
}
