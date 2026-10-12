<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Contracts\ThumbnailGeneratorInterface;
use Imagick;
use ImagickPixel;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InterventionThumbnailGeneratorTest extends TestCase
{
    /** @var list<string> */
    private array $files = [];

    protected function tearDown(): void
    {
        array_map(unlink(...), $this->files);

        parent::tearDown();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function formats(): array
    {
        return [
            'jpg' => ['jpeg'],
            'png' => ['png'],
            'webp' => ['webp'],
            'tiff' => ['tiff'],
            'bmp' => ['bmp'],
        ];
    }

    #[DataProvider('formats')]
    public function test_it_creates_webp_thumbnail_from_every_supported_format(string $format): void
    {
        $thumbnail = $this->generator()->generate($this->makeImage(600, 600, $format));

        $this->assertSame([300, 300, 'image/webp'], $this->describe($thumbnail));
    }

    public function test_it_scales_longer_side_to_300_px_and_keeps_aspect_ratio(): void
    {
        $thumbnail = $this->generator()->generate($this->makeImage(1200, 600, 'png'));

        $this->assertSame([300, 150, 'image/webp'], $this->describe($thumbnail));
    }

    private function generator(): ThumbnailGeneratorInterface
    {
        return $this->app->make(ThumbnailGeneratorInterface::class);
    }

    private function makeImage(int $width, int $height, string $format): string
    {
        $image = new Imagick;
        $image->newImage($width, $height, new ImagickPixel('#3366cc'));
        $image->setImageFormat($format);

        $path = sys_get_temp_dir() . '/thumbnail-source-' . uniqid() . '.' . $format;
        $image->writeImage($path);
        $this->files[] = $path;

        return $path;
    }

    /**
     * @return array{0: int, 1: int, 2: string}
     */
    private function describe(string $contents): array
    {
        $size = getimagesizefromstring($contents);
        $this->assertIsArray($size);

        return [$size[0], $size[1], $size['mime']];
    }
}
