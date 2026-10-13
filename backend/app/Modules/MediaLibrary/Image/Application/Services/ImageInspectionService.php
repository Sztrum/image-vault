<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Services;

use App\Modules\MediaLibrary\Image\Domain\DTO\NewImageDTO;
use App\Modules\MediaLibrary\Image\Domain\DTO\UploadImageDTO;
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

readonly class ImageInspectionService
{
    /**
     * @throws UnsupportedImageFormatException
     * @throws EmptyImageException
     * @throws ImageTooLargeException
     * @throws ImageTooSmallException
     * @throws InvalidImageNameException
     * @throws InvalidUploaderNameException
     * @throws InvalidEmailException
     */
    public function toNewImageDTO(UploadImageDTO $uploadImageDTO): NewImageDTO
    {
        $info = @getimagesize($uploadImageDTO->file_path);
        $size = filesize($uploadImageDTO->file_path);
        throw_if($info === false || $size === false, UnsupportedImageFormatException::class, null, [
            'mime' => __('image::exceptions.unknown_mime'),
            'formats' => implode(', ', ImageFormatEnum::values()),
        ]);

        return new NewImageDTO(
            original_name: new ImageName($uploadImageDTO->original_name),
            format: ImageFormatEnum::fromMimeType($info['mime']),
            size: new FileSize($size),
            dimensions: new ImageDimensions(width: $info[0], height: $info[1]),
            uploader: new Uploader(
                name: $uploadImageDTO->uploader_name,
                email: new Email($uploadImageDTO->uploader_email),
            ),
        );
    }
}
