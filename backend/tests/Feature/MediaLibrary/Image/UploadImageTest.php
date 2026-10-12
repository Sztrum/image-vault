<?php

declare(strict_types=1);

namespace Tests\Feature\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\EloquentImageRepository;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Imagick;
use ImagickPixel;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class UploadImageTest extends TestCase
{
    use RefreshDatabase;

    private const string URL = '/api/v1/images';

    private const string OPEN_METEO = 'api.open-meteo.com/*';

    private Filesystem $disk;

    private bool $weatherAvailable = true;

    /** @var list<string> */
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost:8000', 'app.debug' => false]);
        $this->disk = Storage::fake('local');
        Http::preventStrayRequests();
        Http::fake([self::OPEN_METEO => fn () => $this->weatherAvailable
            ? Http::response([
                'hourly' => [
                    'time' => ['2026-10-12T13:00', '2026-10-12T14:00'],
                    'temperature_2m' => [11.2, 12.4],
                ],
            ])
            : Http::response('Internal Server Error', 500)]);
        $this->travelTo(CarbonImmutable::parse('2026-10-12 14:37:00', 'Europe/Warsaw'));
    }

    protected function tearDown(): void
    {
        array_map(unlink(...), $this->files);

        parent::tearDown();
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function formats(): array
    {
        return [
            'jpg' => ['jpeg', 'photo.jpg', 'jpg'],
            'png' => ['png', 'photo.png', 'png'],
            'webp' => ['webp', 'photo.webp', 'webp'],
            'tiff' => ['tiff', 'photo.tiff', 'tiff'],
            'bmp' => ['bmp', 'photo.bmp', 'bmp'],
        ];
    }

    #[DataProvider('formats')]
    public function test_it_uploads_every_supported_format(string $imagickFormat, string $name, string $extension): void
    {
        $response = $this->upload($this->makeImage(800, 600, $imagickFormat, $name));

        $image = Image::query()->sole();
        $response->assertCreated()
            ->assertExactJson([
                'status' => 201,
                'message' => 'Obraz został dodany.',
                'data' => [
                    'id' => $image->id,
                    'original_name' => $name,
                    'extension' => $extension,
                    'size_bytes' => $image->size_bytes,
                    'width' => 800,
                    'height' => 600,
                    'uploader_name' => 'Anna',
                    'thumbnail_url' => "http://localhost:8000/api/v1/images/{$image->id}/thumbnail",
                    'download_url' => "http://localhost:8000/api/v1/images/{$image->id}/download",
                    'created_at' => '2026-10-12T14:37:00+02:00',
                ],
            ]);
        $this->assertSame('anna@example.com', $image->uploader_email);
        $this->assertMatchesRegularExpression("#^images/[0-9a-f-]{36}\\.{$extension}$#", $image->storage_path);
        $this->assertMatchesRegularExpression('#^thumbnails/[0-9a-f-]{36}\\.webp$#', $image->thumbnail_path);
        $this->disk->assertExists([$image->storage_path, $image->thumbnail_path]);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invalidRequests(): array
    {
        return [
            'unsupported type' => ['gif', 'Dozwolone formaty: JPG, PNG, WebP, TIFF, BMP.'],
            'larger than 5 MB' => ['too_large', 'Obraz może mieć najwyżej 5 MB.'],
            'smaller than 500 px' => ['too_small', 'Obraz musi mieć co najmniej 500×500 px.'],
            'missing name' => ['missing_name', 'Podaj imię.'],
            'missing email' => ['missing_email', 'Podaj adres e-mail.'],
            'invalid email' => ['invalid_email', 'Podaj poprawny adres e-mail.'],
        ];
    }

    #[DataProvider('invalidRequests')]
    public function test_it_rejects_invalid_request(string $case, string $message): void
    {
        $file = match ($case) {
            'gif' => $this->makeImage(600, 600, 'gif', 'photo.gif'),
            'too_large' => $this->makeImage(600, 600, 'png', 'photo.png', padToBytes: 5 * 1024 * 1024 + 1),
            'too_small' => $this->makeImage(499, 600, 'png', 'photo.png'),
            default => $this->makeImage(600, 600, 'png', 'photo.png'),
        };
        $fields = match ($case) {
            'missing_name' => ['uploader_name' => ''],
            'missing_email' => ['uploader_email' => ''],
            'invalid_email' => ['uploader_email' => 'not-an-email'],
            default => [],
        };
        $field = match ($case) {
            'missing_name' => 'uploader_name',
            'missing_email', 'invalid_email' => 'uploader_email',
            default => 'file',
        };

        $this->upload($file, $fields)
            ->assertUnprocessable()
            ->assertExactJson([
                'status' => 422,
                'message' => 'Nieprawidłowe dane.',
                'errors' => [$field => [$message]],
            ]);
        $this->assertDatabaseEmpty('images');
        $this->assertSame([], $this->disk->allFiles());
    }

    public function test_it_stores_image_without_temperature_when_weather_is_unavailable(): void
    {
        $this->weatherAvailable = false;

        $this->upload($this->makeImage(600, 600, 'png', 'photo.png'))->assertCreated();

        $image = Image::query()->sole();
        $this->assertNull($image->temperature_celsius);
        $this->assertNull($image->temperature_measured_at);
    }

    public function test_it_stores_temperature_for_current_hour(): void
    {
        $this->upload($this->makeImage(600, 600, 'png', 'photo.png'))->assertCreated();

        $image = Image::query()->sole();
        $this->assertSame('12.4', $image->temperature_celsius);
        $this->assertSame('2026-10-12 14:00:00', $image->temperature_measured_at?->format('Y-m-d H:i:s'));
    }

    public function test_it_stores_exif_and_iptc_metadata(): void
    {
        $fixture = base_path('tests/Fixtures/images/exif-iptc.jpg');

        $this->upload(new UploadedFile($fixture, 'exif-iptc.jpg', null, null, true))->assertCreated();

        $image = Image::query()->sole();
        $this->assertSame('ImageVault Camera', data_get($image->exif, 'IFD0.Make'));
        $this->assertSame(['Fixture title'], data_get($image->iptc, '2#005'));
    }

    public function test_it_removes_stored_files_when_saving_to_database_fails(): void
    {
        $this->partialMock(EloquentImageRepository::class, function (MockInterface $mock): void {
            $mock->shouldReceive('save')->andThrow(new RuntimeException('Database is down'));
        });

        $this->upload($this->makeImage(600, 600, 'png', 'photo.png'))->assertInternalServerError();

        $this->assertDatabaseEmpty('images');
        $this->assertSame([], $this->disk->allFiles());
    }

    /**
     * @param  array<string, string>  $fields
     * @return TestResponse<JsonResponse>
     */
    private function upload(UploadedFile $file, array $fields = []): TestResponse
    {
        return $this->post(self::URL, [
            'file' => $file,
            'uploader_name' => 'Anna',
            'uploader_email' => 'anna@example.com',
            ...$fields,
        ], ['Accept' => 'application/json']);
    }

    private function makeImage(int $width, int $height, string $format, string $name, ?int $padToBytes = null): UploadedFile
    {
        $image = new Imagick;
        $image->newImage($width, $height, new ImagickPixel('#3366cc'));
        $image->setImageFormat($format);

        $path = sys_get_temp_dir() . '/upload-' . uniqid() . '-' . $name;
        $image->writeImage($path);
        $this->files[] = $path;

        if ($padToBytes !== null) {
            // Trailing bytes keep the file a valid image while pushing it over the size limit.
            file_put_contents($path, str_repeat("\0", $padToBytes - (int) filesize($path)), FILE_APPEND);
        }

        return new UploadedFile($path, $name, null, null, true);
    }
}
