<?php

declare(strict_types=1);

namespace Tests\Feature\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImageFilesTest extends TestCase
{
    use RefreshDatabase;

    private const string MISSING_ID = '0199d0a0-0000-7000-8000-000000000000';

    private Filesystem $disk;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);
        $this->disk = Storage::fake('local');
    }

    public function test_it_serves_thumbnail_as_webp(): void
    {
        $image = Image::factory()->create();
        $this->disk->put($image->thumbnail_path, 'webp-bytes');

        $response = $this->get("/api/v1/images/{$image->id}/thumbnail")->assertOk();

        $this->assertSame('image/webp', $response->headers->get('Content-Type'));
        $this->assertSame('webp-bytes', $response->streamedContent());
    }

    public function test_it_downloads_original_with_its_original_name(): void
    {
        $image = Image::factory()->create([
            'original_name' => 'góry.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
        ]);
        $this->disk->put($image->storage_path, 'jpeg-bytes');

        $response = $this->get("/api/v1/images/{$image->id}/download")->assertOk();

        $this->assertSame('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertSame("attachment; filename=gory.jpg; filename*=utf-8''g%C3%B3ry.jpg", $response->headers->get('Content-Disposition'));
        $this->assertSame('jpeg-bytes', $response->streamedContent());
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function endpointsForMissingImage(): array
    {
        return [
            'thumbnail' => ['get', '/thumbnail'],
            'download' => ['get', '/download'],
            'delete' => ['delete', ''],
        ];
    }

    #[DataProvider('endpointsForMissingImage')]
    public function test_it_returns_404_for_missing_image(string $method, string $suffix): void
    {
        $this->json($method, '/api/v1/images/' . self::MISSING_ID . $suffix)
            ->assertNotFound()
            ->assertExactJson(['status' => 404, 'message' => 'Nie znaleziono obrazu.']);
    }
}
