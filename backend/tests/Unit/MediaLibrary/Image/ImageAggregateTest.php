<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Aggregates\ImageAggregate;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormat;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidImageNameException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Shared\Domain\ValueObjects\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImageAggregateTest extends TestCase
{
    public function test_it_registers_new_image(): void
    {
        $uploader = new Uploader('Anna', new Email('anna@example.com'));

        $image = ImageAggregate::register(
            ' gory.jpg ',
            ImageFormat::JPG,
            new FileSize(2_483_011),
            new ImageDimensions(4000, 3000),
            $uploader,
        );

        $this->assertSame('gory.jpg', $image->original_name);
        $this->assertSame(ImageFormat::JPG, $image->format);
        $this->assertSame(2_483_011, $image->size->bytes);
        $this->assertSame(4000, $image->dimensions->width);
        $this->assertSame($uploader, $image->uploader);
    }

    #[DataProvider('invalidNames')]
    public function test_it_rejects_invalid_original_name(string $name): void
    {
        $this->expectException(InvalidImageNameException::class);

        ImageAggregate::register(
            $name,
            ImageFormat::PNG,
            new FileSize(1024),
            new ImageDimensions(600, 600),
            new Uploader('Anna', new Email('anna@example.com')),
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidNames(): array
    {
        return [
            'empty' => [' '],
            '256 characters' => [str_repeat('a', 252) . '.png'],
        ];
    }
}
