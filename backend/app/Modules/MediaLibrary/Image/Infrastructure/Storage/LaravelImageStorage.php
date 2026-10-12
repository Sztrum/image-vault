<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Infrastructure\Storage;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use RuntimeException;

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
    public function storeOriginal(string $imageId, ImageFormatEnum $format, string $sourcePath): string
    {
        $path = $this->disk()->putFileAs(self::ORIGINALS_DIRECTORY, new File($sourcePath), $imageId . '.' . $format->value);
        throw_if($path === false, RuntimeException::class, 'Could not store the original image.');

        return $path;
    }

    /**
     * @return string storage path of the thumbnail
     *
     * @throws RuntimeException
     */
    public function storeThumbnail(string $imageId, string $contents): string
    {
        $path = self::THUMBNAILS_DIRECTORY . '/' . $imageId . '.webp';
        throw_unless($this->disk()->put($path, $contents), RuntimeException::class, 'Could not store the thumbnail.');

        return $path;
    }

    public function absolutePath(string $storagePath): string
    {
        return $this->disk()->path($storagePath);
    }

    public function delete(string ...$storagePaths): void
    {
        $this->disk()->delete($storagePaths);
    }

    private function disk(): Filesystem
    {
        return $this->filesystem->disk($this->config->string('image::image.disk'));
    }
}
