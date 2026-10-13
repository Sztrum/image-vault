<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Translation key of the request's custom validation messages, e.g. 'image::validation.custom'.
     */
    protected const string MESSAGES_KEY = '';

    protected $stopOnFirstFailure = true;

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = static::MESSAGES_KEY !== '' ? __(static::MESSAGES_KEY) : [];

        if (!is_array($messages)) {
            return [];
        }

        /** @var array<string, string> $messages */
        return $messages;
    }
}
