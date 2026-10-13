<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Requests;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageName;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\UI\Http\Requests\BaseRequest;
use Closure;
use Illuminate\Http\UploadedFile;

class ImageStoreRequest extends BaseRequest
{
    protected const string MESSAGES_KEY = 'image::validation.custom';

    /**
     * @return array<string, list<string|Closure>>
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
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value instanceof UploadedFile && mb_strlen($value->getClientOriginalName()) > ImageName::MAX_LENGTH) {
                        $fail(__('image::validation.file_name_max'));
                    }
                },
            ],
            'uploader_name' => ['required', 'string', 'max:' . Uploader::NAME_MAX_LENGTH],
            'uploader_email' => ['required', 'string', 'email:filter', 'max:' . Email::MAX_LENGTH],
        ];
    }
}
