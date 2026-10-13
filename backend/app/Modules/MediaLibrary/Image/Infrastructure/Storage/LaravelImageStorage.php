<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Infrastructure\Storage;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\File;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaravelImageStorage
{
    private const string ORIGINALS_DIRECTORY = 'images';

    private const string THUMBNAILS_DIRECTORY = 'thumbnails';

    public function __construct(
        private readonly FilesystemFactory $filesystem,
        private readonly ConfigContract $config,
    ) {}

    /**
     * @return string storage path of the original
     *
     * @throws RuntimeException
     */
    public function storeOriginal(string $fileName, ImageFormatEnum $format, string $sourcePath): string
    {
        $path = $this->disk()->putFileAs(self::ORIGINALS_DIRECTORY, new File($sourcePath), $fileName . '.' . $format->value);
        throw_if($path === false, RuntimeException::class, 'Could not store the original image.');

        return $path;
    }

    /**
     * @return string storage path of the thumbnail
     *
     * @throws RuntimeException
     */
    public function storeThumbnail(string $fileName, string $contents): string
    {
        $path = self::THUMBNAILS_DIRECTORY . '/' . $fileName . '.webp';
        throw_unless($this->disk()->put($path, $contents), RuntimeException::class, 'Could not store the thumbnail.');

        return $path;
    }

    public function delete(string ...$storagePaths): void
    {
        $this->disk()->delete($storagePaths);
    }

    /**
     * @param  array<string, string>  $headers
     */
    public function response(string $storagePath, array $headers = []): StreamedResponse
    {
        return $this->disk()->response(path: $storagePath, headers: $headers);
    }

    /**
     * @param  array<string, string>  $headers
     */
    public function download(string $storagePath, string $name, array $headers = []): StreamedResponse
    {
        return $this->disk()->download(path: $storagePath, name: $name, headers: $headers);
    }

    private function disk(): FilesystemAdapter
    {
        $disk = $this->filesystem->disk($this->config->string('image::image.disk'));
        throw_unless($disk instanceof FilesystemAdapter, RuntimeException::class, 'Image disk must be a filesystem adapter.');

        return $disk;
    }
}
