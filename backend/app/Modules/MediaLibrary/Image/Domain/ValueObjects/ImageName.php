<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\ValueObjects;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidImageNameException;

final readonly class ImageName
{
    public const int MAX_LENGTH = 255;

    public string $value;

    /**
     * @throws InvalidImageNameException
     */
    public function __construct(string $value)
    {
        $value = trim($value);
        throw_if($value === '' || mb_strlen($value) > self::MAX_LENGTH, InvalidImageNameException::class);

        $this->value = $value;
    }
}
