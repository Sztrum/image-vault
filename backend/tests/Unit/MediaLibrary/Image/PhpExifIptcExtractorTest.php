<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Infrastructure\Metadata\PhpExifIptcExtractor;
use Imagick;
use ImagickPixel;
use Tests\TestCase;

class PhpExifIptcExtractorTest extends TestCase
{
    public function test_it_extracts_exif_and_iptc_from_jpeg_as_json_safe_arrays(): void
    {
        $metadata = $this->extractor()->extract(base_path('tests/Fixtures/images/exif-iptc.jpg'), ImageFormatEnum::JPG);

        $this->assertSame('ImageVault Camera', $metadata->exif['IFD0']['Make'] ?? null);
        $this->assertSame('2026:10:12 14:00:00', $metadata->exif['EXIF']['DateTimeOriginal'] ?? null);
        $this->assertStringStartsWith('base64:', $metadata->exif['EXIF']['MakerNote'] ?? '');
        $this->assertSame(['Fixture title'], $metadata->iptc['2#005'] ?? null);
        $this->assertSame(['test', 'fixture'], $metadata->iptc['2#025'] ?? null);
        $this->assertJson(json_encode([$metadata->exif, $metadata->iptc], JSON_THROW_ON_ERROR));
    }

    public function test_it_returns_nulls_for_image_without_metadata(): void
    {
        $image = new Imagick;
        $image->newImage(600, 600, new ImagickPixel('#3366cc'));
        $image->setImageFormat('png');
        $path = sys_get_temp_dir() . '/no-metadata-' . uniqid() . '.png';
        $image->writeImage($path);

        $metadata = $this->extractor()->extract($path, ImageFormatEnum::PNG);

        $this->assertNull($metadata->exif);
        $this->assertNull($metadata->iptc);
        unlink($path);
    }

    private function extractor(): PhpExifIptcExtractor
    {
        return $this->app->make(PhpExifIptcExtractor::class);
    }
}
