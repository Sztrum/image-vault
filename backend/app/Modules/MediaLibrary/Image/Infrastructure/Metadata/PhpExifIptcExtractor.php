<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Infrastructure\Metadata;

use App\Modules\MediaLibrary\Image\Domain\DTO\ImageMetadataDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;

class PhpExifIptcExtractor
{
    private const array EXIF_FORMATS = [ImageFormatEnum::JPG, ImageFormatEnum::TIFF];

    private const string BINARY_PREFIX = 'base64:';

    public function extract(string $sourcePath, ImageFormatEnum $format): ImageMetadataDTO
    {
        return new ImageMetadataDTO(
            exif: $this->exif($sourcePath, $format),
            iptc: $this->iptc($sourcePath),
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function exif(string $sourcePath, ImageFormatEnum $format): ?array
    {
        if (!in_array($format, self::EXIF_FORMATS, true)) {
            return null;
        }

        // Corrupted EXIF blocks raise warnings; metadata is optional, so they must not fail the upload.
        $exif = @exif_read_data($sourcePath, null, true);

        return is_array($exif) && $exif !== [] ? $this->toJsonSafe($exif) : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function iptc(string $sourcePath): ?array
    {
        $info = [];
        @getimagesize($sourcePath, $info);
        if (!isset($info['APP13'])) {
            return null;
        }

        $iptc = iptcparse($info['APP13']);

        return is_array($iptc) && $iptc !== [] ? $this->toJsonSafe($iptc) : null;
    }

    /**
     * Binary values (e.g. EXIF MakerNote) are not valid UTF-8 and would break json_encode.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<string, mixed>
     */
    private function toJsonSafe(array $data): array
    {
        $safe = [];
        foreach ($data as $key => $value) {
            $safe[(string) $key] = match (true) {
                is_array($value) => $this->toJsonSafe($value),
                is_string($value) && !mb_check_encoding($value, 'UTF-8') => self::BINARY_PREFIX . base64_encode($value),
                default => $value,
            };
        }

        return $safe;
    }
}
