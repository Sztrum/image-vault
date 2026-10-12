<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\UnsupportedImageFormatException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImageFormatEnumTest extends TestCase
{
    #[DataProvider('supportedMimeTypes')]
    public function test_it_maps_supported_mime_type(string $mimeType, ImageFormatEnum $expected): void
    {
        $this->assertSame($expected, ImageFormatEnum::fromMimeType($mimeType));
    }

    /**
     * @return array<string, array{string, ImageFormatEnum}>
     */
    public static function supportedMimeTypes(): array
    {
        return [
            'jpeg' => ['image/jpeg', ImageFormatEnum::JPG],
            'png' => ['image/png', ImageFormatEnum::PNG],
            'webp' => ['image/webp', ImageFormatEnum::WEBP],
            'tiff' => ['image/tiff', ImageFormatEnum::TIFF],
            'bmp' => ['image/bmp', ImageFormatEnum::BMP],
            'legacy bmp' => ['image/x-ms-bmp', ImageFormatEnum::BMP],
        ];
    }

    public function test_it_rejects_unsupported_mime_type(): void
    {
        $this->expectException(UnsupportedImageFormatException::class);

        ImageFormatEnum::fromMimeType('image/gif');
    }
}
