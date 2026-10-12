<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;

final class InvalidUploaderNameException extends DomainException
{
    public function __construct()
    {
        parent::__construct('image::exceptions.invalid_uploader_name', [
            'max' => Uploader::NAME_MAX_LENGTH,
        ]);
    }
}
