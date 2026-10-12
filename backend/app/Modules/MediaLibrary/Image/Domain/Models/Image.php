<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Models;

use App\Core\Domain\Models\Model;
use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property string $original_name
 * @property string $extension
 * @property string $mime_type
 * @property int $size_bytes
 * @property int $width
 * @property int $height
 * @property string $storage_path
 * @property string $thumbnail_path
 * @property string $uploader_name
 * @property string $uploader_email
 * @property array<string, mixed>|null $exif
 * @property array<string, mixed>|null $iptc
 * @property string|null $temperature_celsius
 * @property Carbon|null $temperature_measured_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[UseFactory(ImageFactory::class)]
class Image extends Model
{
    /** @use HasFactory<ImageFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'exif' => 'array',
            'iptc' => 'array',
            'temperature_celsius' => 'decimal:1',
            'temperature_measured_at' => 'datetime',
        ];
    }
}
