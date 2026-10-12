<?php

declare(strict_types=1);

namespace Tests\Feature\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_record_and_both_files(): void
    {
        $disk = Storage::fake('local');
        $image = Image::factory()->create();
        $kept = Image::factory()->create();
        $disk->put($image->storage_path, 'original');
        $disk->put($image->thumbnail_path, 'thumbnail');
        $disk->put($kept->storage_path, 'original');

        $this->deleteJson("/api/v1/images/{$image->id}")->assertNoContent();

        $this->assertModelMissing($image);
        $this->assertModelExists($kept);
        $disk->assertMissing([$image->storage_path, $image->thumbnail_path]);
        $disk->assertExists($kept->storage_path);
    }
}
