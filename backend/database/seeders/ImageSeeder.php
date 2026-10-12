<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\MediaLibrary\Image\Application\Services\ImageFileService;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use Illuminate\Database\Seeder;
use Imagick;

class ImageSeeder extends Seeder
{
    private const int COUNT = 25;

    public function __construct(
        private readonly ImageFileService $imageFileService,
    ) {}

    public function run(): void
    {
        foreach (range(1, self::COUNT) as $number) {
            $width = fake()->numberBetween(600, 1600);
            $height = fake()->numberBetween(600, 1600);
            $filePath = $this->makeImage(width: $width, height: $height, number: $number);

            $storedFiles = $this->imageFileService->storeFiles(filePath: $filePath, format: ImageFormatEnum::JPG);

            Image::factory()->create([
                'original_name' => "seed-{$number}.jpg",
                'extension' => ImageFormatEnum::JPG->value,
                'mime_type' => ImageFormatEnum::JPG->mimeType(),
                'size_bytes' => filesize($filePath),
                'width' => $width,
                'height' => $height,
                'storage_path' => $storedFiles->original_path,
                'thumbnail_path' => $storedFiles->thumbnail_path,
                'created_at' => now()->subMinutes(self::COUNT - $number),
            ]);

            unlink($filePath);
        }
    }

    private function makeImage(int $width, int $height, int $number): string
    {
        $image = new Imagick;
        $image->newPseudoImage($width, $height, sprintf('gradient:%s-%s', fake()->hexColor(), fake()->hexColor()));
        $image->setImageFormat('jpeg');

        $filePath = sys_get_temp_dir() . "/image-seed-{$number}.jpg";
        $image->writeImage($filePath);

        return $filePath;
    }
}
