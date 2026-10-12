<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\HttpException;
use Symfony\Component\HttpFoundation\Response;

final class ImageNotFoundException extends HttpException
{
    /**
     * @see backend/app/Modules/MediaLibrary/Image/Application/Translations/pl/exceptions.php
     */
    private const string MESSAGE_KEY = 'image::exceptions.not_found';

    public function __construct()
    {
        parent::__construct(message: self::resolveDefaultMessage(self::MESSAGE_KEY), statusCode: Response::HTTP_NOT_FOUND);
    }
}
