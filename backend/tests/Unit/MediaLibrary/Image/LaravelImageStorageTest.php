<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Infrastructure\Storage\LaravelImageStorage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaravelImageStorageTest extends TestCase
{
    public function test_it_stores_original_and_thumbnail_and_deletes_both(): void
    {
        Storage::fake('local');
        $storage = $this->app->make(LaravelImageStorage::class);
        $source = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($source, 'original-bytes');

        $originalPath = $storage->storeOriginal('0199d0a0-0000-7000-8000-000000000001', ImageFormatEnum::TIFF, $source);
        $thumbnailPath = $storage->storeThumbnail('0199d0a0-0000-7000-8000-000000000001', 'webp-bytes');

        $this->assertSame('images/0199d0a0-0000-7000-8000-000000000001.tiff', $originalPath);
        $this->assertSame('thumbnails/0199d0a0-0000-7000-8000-000000000001.webp', $thumbnailPath);
        Storage::disk('local')->assertExists($originalPath);
        $this->assertSame('webp-bytes', Storage::disk('local')->get($thumbnailPath));

        $storage->delete($originalPath, $thumbnailPath);

        Storage::disk('local')->assertMissing([$originalPath, $thumbnailPath]);
        unlink($source);
    }
}
