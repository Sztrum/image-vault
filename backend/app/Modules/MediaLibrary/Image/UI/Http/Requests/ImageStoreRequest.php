<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Requests;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\UI\Http\Requests\BaseRequest;

class ImageStoreRequest extends BaseRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                // jpeg and tif are the extensions guessed from the file content for jpg and tiff.
                'mimes:' . implode(',', [...ImageFormatEnum::values(), 'jpeg', 'tif']),
                'max:' . FileSize::MAX_BYTES / 1024,
                'dimensions:min_width=' . ImageDimensions::MIN_SIDE . ',min_height=' . ImageDimensions::MIN_SIDE,
            ],
            'uploader_name' => ['required', 'string', 'max:' . Uploader::NAME_MAX_LENGTH],
            'uploader_email' => ['required', 'string', 'email', 'max:' . Email::MAX_LENGTH],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = __('image::validation.custom');

        if (!is_array($messages)) {
            return [];
        }

        /** @var array<string, string> $messages */
        return $messages;
    }
}
