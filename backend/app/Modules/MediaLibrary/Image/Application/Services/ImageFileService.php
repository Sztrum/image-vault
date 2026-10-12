<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Services;

use App\Modules\MediaLibrary\Image\Domain\DTO\NewImageDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\EmptyImageException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooLargeException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooSmallException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidImageNameException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidUploaderNameException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\UnsupportedImageFormatException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageName;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Shared\Domain\Exceptions\InvalidEmailException;
use App\Shared\Domain\ValueObjects\Email;

readonly class ImageFileService
{
    /**
     * Reads format, size and dimensions from the file itself, not from what the client sent.
     *
     * @throws UnsupportedImageFormatException
     * @throws EmptyImageException
     * @throws ImageTooLargeException
     * @throws ImageTooSmallException
     * @throws InvalidImageNameException
     * @throws InvalidUploaderNameException
     * @throws InvalidEmailException
     */
    public function toNewImageDTO(
        string $filePath,
        string $originalName,
        string $uploaderName,
        string $uploaderEmail,
    ): NewImageDTO {
        $info = @getimagesize($filePath);
        $size = filesize($filePath);
        throw_if($info === false || $size === false, UnsupportedImageFormatException::class, null, [
            'mime' => 'unknown',
            'formats' => implode(', ', ImageFormatEnum::values()),
        ]);

        return new NewImageDTO(
            original_name: new ImageName($originalName),
            format: ImageFormatEnum::fromMimeType($info['mime']),
            size: new FileSize($size),
            dimensions: new ImageDimensions(width: $info[0], height: $info[1]),
            uploader: new Uploader(name: $uploaderName, email: new Email($uploaderEmail)),
        );
    }
}
