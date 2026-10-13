<?php

declare(strict_types=1);

namespace Tests\Feature\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ListImagesTest extends TestCase
{
    use RefreshDatabase;

    private const string URL = '/api/v1/images';

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost:8000']);
    }

    public function test_it_lists_newest_images_first_with_contract_fields_only(): void
    {
        $older = Image::factory()->create(['created_at' => Carbon::parse('2026-10-12 10:00:00')]);
        $newer = Image::factory()->create([
            'original_name' => 'góry.jpg',
            'extension' => 'jpg',
            'size_bytes' => 2483011,
            'width' => 4000,
            'height' => 3000,
            'uploader_name' => 'Anna',
            'exif' => ['IFD0' => ['Make' => 'Camera']],
            'temperature_celsius' => 12.4,
            'created_at' => Carbon::parse('2026-10-12 12:15:00'),
        ]);

        $response = $this->getJson(self::URL)->assertOk();

        $this->assertSame([$newer->id, $older->id], $response->json('data.*.id'));
        $this->assertSame([
            'id' => $newer->id,
            'original_name' => 'góry.jpg',
            'extension' => 'jpg',
            'size_bytes' => 2483011,
            'width' => 4000,
            'height' => 3000,
            'uploader_name' => 'Anna',
            'thumbnail_url' => "http://localhost:8000/api/v1/images/{$newer->id}/thumbnail",
            'download_url' => "http://localhost:8000/api/v1/images/{$newer->id}/download",
            'created_at' => '2026-10-12T12:15:00+02:00',
        ], $response->json('data.0'));
        $response->assertJsonPath('status', 200)->assertJsonPath('meta.next_cursor', null);
    }

    public function test_it_walks_through_all_pages_by_cursor_without_duplicates(): void
    {
        $createdAt = Carbon::parse('2026-10-12 10:00:00');
        $images = Image::factory()
            ->count(25)
            ->state(new Sequence(fn (Sequence $sequence): array => [
                // Every third image shares a timestamp, so the id tiebreaker is exercised too.
                'created_at' => $createdAt->copy()->addMinutes(intdiv($sequence->index, 3)),
            ]))
            ->create();

        $pageSizes = [];
        $seenIds = [];
        $cursor = null;
        do {
            $response = $this->getJson(self::URL . '?' . http_build_query(['per_page' => 10, 'cursor' => $cursor]))->assertOk();
            $ids = $response->json('data.*.id');
            $pageSizes[] = count($ids);
            $seenIds = [...$seenIds, ...$ids];
            $cursor = $response->json('meta.next_cursor');
        } while ($cursor !== null);

        $this->assertSame([10, 10, 5], $pageSizes);
        $this->assertSame(
            $images->sortByDesc(fn (Image $image): string => $image->created_at->format('YmdHis') . $image->id)->pluck('id')->all(),
            $seenIds,
        );
    }

    public function test_it_returns_ten_images_per_page_by_default(): void
    {
        Image::factory()->count(12)->create();

        $this->getJson(self::URL)
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_it_rejects_per_page_above_fifty(): void
    {
        $this->getJson(self::URL . '?per_page=51')
            ->assertUnprocessable()
            ->assertJsonPath('errors.per_page.0', 'Liczba obrazów na stronę musi być liczbą od 1 do 50.');
    }

    public function test_it_rejects_a_tampered_cursor(): void
    {
        Image::factory()->create();
        $cursor = rtrim(strtr(base64_encode((string) json_encode(['foo' => 1, '_pointsToNextItems' => true])), '+/', '-_'), '=');

        $this->getJson(self::URL . '?cursor=' . $cursor)
            ->assertUnprocessable()
            ->assertJsonPath('errors.cursor.0', 'Niepoprawny kursor.');
    }
}
