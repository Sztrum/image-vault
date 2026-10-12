<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;

final class UnsupportedImageFormatException extends DomainException
{
    /**
     * @see backend/app/Modules/MediaLibrary/Image/Application/Translations/pl/exceptions.php
     */
    protected const string DEFAULT_MESSAGE_KEY = 'image::exceptions.unsupported_format';
}
