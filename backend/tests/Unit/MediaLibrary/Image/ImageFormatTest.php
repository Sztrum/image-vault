<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormat;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\UnsupportedImageFormatException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImageFormatTest extends TestCase
{
    #[DataProvider('supportedMimeTypes')]
    public function test_it_maps_supported_mime_type(string $mimeType, ImageFormat $expected): void
    {
        $this->assertSame($expected, ImageFormat::fromMimeType($mimeType));
    }

    /**
     * @return array<string, array{string, ImageFormat}>
     */
    public static function supportedMimeTypes(): array
    {
        return [
            'jpeg' => ['image/jpeg', ImageFormat::JPG],
            'png' => ['image/png', ImageFormat::PNG],
            'webp' => ['image/webp', ImageFormat::WEBP],
            'tiff' => ['image/tiff', ImageFormat::TIFF],
            'bmp' => ['image/bmp', ImageFormat::BMP],
            'legacy bmp' => ['image/x-ms-bmp', ImageFormat::BMP],
        ];
    }

    public function test_it_rejects_unsupported_mime_type(): void
    {
        $this->expectException(UnsupportedImageFormatException::class);

        ImageFormat::fromMimeType('image/gif');
    }
}
