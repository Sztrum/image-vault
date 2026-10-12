<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\DTO;

use App\Shared\Domain\DTO\AbstractDTO;

final class ImageMetadataDTO extends AbstractDTO
{
    /**
     * @param  array<string, mixed>|null  $exif
     * @param  array<string, mixed>|null  $iptc
     */
    public function __construct(
        public ?array $exif,
        public ?array $iptc,
    ) {}
}
