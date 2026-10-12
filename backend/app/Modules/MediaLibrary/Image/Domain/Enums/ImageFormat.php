<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Enums;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\UnsupportedImageFormatException;

enum ImageFormat: string
{
    case JPG = 'jpg';
    case PNG = 'png';
    case WEBP = 'webp';
    case TIFF = 'tiff';
    case BMP = 'bmp';

    /**
     * @throws UnsupportedImageFormatException
     */
    public static function fromMimeType(string $mimeType): self
    {
        return match (strtolower($mimeType)) {
            'image/jpeg' => self::JPG,
            'image/png' => self::PNG,
            'image/webp' => self::WEBP,
            'image/tiff' => self::TIFF,
            'image/bmp', 'image/x-ms-bmp' => self::BMP,
            default => throw new UnsupportedImageFormatException($mimeType),
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::JPG => 'image/jpeg',
            self::PNG => 'image/png',
            self::WEBP => 'image/webp',
            self::TIFF => 'image/tiff',
            self::BMP => 'image/bmp',
        };
    }
}
