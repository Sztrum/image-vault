<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\ImageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ImageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_paginates_newest_first_with_id_as_tiebreaker_and_no_duplicates(): void
    {
        $oldest = $this->createImage('0199d0a0-0000-7000-8000-000000000001', '2026-10-12 10:00:00');
        $tieLower = $this->createImage('0199d0a0-0000-7000-8000-000000000002', '2026-10-12 11:00:00');
        $tieHigher = $this->createImage('0199d0a0-0000-7000-8000-000000000003', '2026-10-12 11:00:00');
        $newest = $this->createImage('0199d0a0-0000-7000-8000-000000000004', '2026-10-12 12:00:00');
        $repository = $this->app->make(ImageRepository::class);

        $firstPage = $repository->paginateByCursor(2, null);
        $secondPage = $repository->paginateByCursor(2, $firstPage->nextCursor()?->encode());

        $this->assertSame([$newest->id, $tieHigher->id], array_column($firstPage->items(), 'id'));
        $this->assertSame([$tieLower->id, $oldest->id], array_column($secondPage->items(), 'id'));
        $this->assertFalse($secondPage->hasMorePages());
    }

    private function createImage(string $id, string $createdAt): Image
    {
        $image = new Image([
            'original_name' => 'photo.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1024,
            'width' => 600,
            'height' => 600,
            'storage_path' => "images/{$id}.jpg",
            'thumbnail_path' => "thumbnails/{$id}.webp",
            'uploader_name' => 'Jan',
            'uploader_email' => 'jan@example.com',
        ]);
        $image->id = $id;
        $image->created_at = $image->updated_at = Carbon::parse($createdAt);
        $image->save();

        return $image;
    }
}
