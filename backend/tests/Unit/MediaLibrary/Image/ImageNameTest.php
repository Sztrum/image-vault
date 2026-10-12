<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidImageNameException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageName;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImageNameTest extends TestCase
{
    public function test_it_trims_name(): void
    {
        $this->assertSame('gory.jpg', (new ImageName(' gory.jpg '))->value);
    }

    public function test_it_accepts_maximum_length(): void
    {
        $name = str_repeat('ą', 251) . '.png';

        $this->assertSame($name, (new ImageName($name))->value);
    }

    #[DataProvider('invalidNames')]
    public function test_it_rejects_invalid_name(string $name): void
    {
        $this->expectException(InvalidImageNameException::class);

        new ImageName($name);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidNames(): array
    {
        return [
            'whitespace only' => ['   '],
            '256 characters' => [str_repeat('a', 252) . '.png'],
        ];
    }
}
