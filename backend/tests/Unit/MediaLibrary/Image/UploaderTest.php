<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidUploaderNameException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Shared\Domain\ValueObjects\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UploaderTest extends TestCase
{
    public function test_it_trims_name(): void
    {
        $uploader = new Uploader('  Anna  ', new Email('anna@example.com'));

        $this->assertSame('Anna', $uploader->name);
    }

    #[DataProvider('invalidNames')]
    public function test_it_rejects_invalid_name(string $name): void
    {
        $this->expectException(InvalidUploaderNameException::class);

        new Uploader($name, new Email('anna@example.com'));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidNames(): array
    {
        return [
            'whitespace only' => ['   '],
            '101 characters' => [str_repeat('ą', 101)],
        ];
    }
}
