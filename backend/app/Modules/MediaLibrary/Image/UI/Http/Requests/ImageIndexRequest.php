<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Requests;

use App\Shared\UI\Http\Requests\BaseRequest;

class ImageIndexRequest extends BaseRequest
{
    protected const string MESSAGES_KEY = 'image::validation.custom';

    public const int DEFAULT_PER_PAGE = 10;

    public const int MAX_PER_PAGE = 50;

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'cursor' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_PER_PAGE],
        ];
    }
}
