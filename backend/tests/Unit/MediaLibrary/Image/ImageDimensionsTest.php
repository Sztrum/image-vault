<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooSmallException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImageDimensionsTest extends TestCase
{
    public function test_it_accepts_minimum_dimensions(): void
    {
        $dimensions = new ImageDimensions(500, 500);

        $this->assertSame(500, $dimensions->width);
        $this->assertSame(500, $dimensions->height);
    }

    #[DataProvider('tooSmallDimensions')]
    public function test_it_rejects_dimensions_below_minimum(int $width, int $height): void
    {
        $this->expectException(ImageTooSmallException::class);

        new ImageDimensions($width, $height);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function tooSmallDimensions(): array
    {
        return [
            'width 499' => [499, 500],
            'height 499' => [500, 499],
        ];
    }

    public function test_exception_is_unprocessable_with_translated_message(): void
    {
        try {
            new ImageDimensions(499, 600);
            $this->fail('ImageTooSmallException was not thrown.');
        } catch (ImageTooSmallException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertSame('Obraz musi mieć co najmniej 500×500 px (przesłany: 499×600 px).', $exception->getMessage());
        }
    }
}
