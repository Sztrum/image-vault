<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    protected $model = Image::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $format = $this->faker->randomElement(ImageFormatEnum::cases());
        $fileName = (string) Str::uuid7();

        return [
            'original_name' => $this->faker->word() . '.' . $format->value,
            'extension' => $format->value,
            'mime_type' => $format->mimeType(),
            'size_bytes' => $this->faker->numberBetween(10_000, 5_000_000),
            'width' => $this->faker->numberBetween(500, 4000),
            'height' => $this->faker->numberBetween(500, 4000),
            'storage_path' => "images/{$fileName}.{$format->value}",
            'thumbnail_path' => "thumbnails/{$fileName}.webp",
            'uploader_name' => $this->faker->firstName(),
            'uploader_email' => $this->faker->safeEmail(),
        ];
    }
}
