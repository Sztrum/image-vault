<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Resources;

class ImageDetailsResource extends ImageResource
{
    public function getResourceData(): array
    {
        $image = $this->getResource();

        return parent::getResourceData() + [
            'uploader_email' => $image->uploader_email,
            'temperature_celsius' => $image->temperature_celsius === null ? null : (float) $image->temperature_celsius,
            'temperature_measured_at' => $image->temperature_measured_at?->toIso8601String(),
            'exif' => $image->exif,
            'iptc' => $image->iptc,
        ];
    }
}
