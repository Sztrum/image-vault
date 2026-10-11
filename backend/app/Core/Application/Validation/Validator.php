<?php

declare(strict_types=1);

namespace App\Core\Application\Validation;

use App\Core\Application\Providers\ModuleServiceProvider;
use App\Core\CoreModuleServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as BaseValidator;

class Validator extends BaseValidator
{
    protected function getMessage($attribute, $rule)
    {
        $lowerRule = Str::snake($rule);

        $key = $this->getValidationTranslationKey(
            parent::getMessage($attribute, $rule)
        );

        if ($key !== ($value = $this->translator->get($key))) {
            return $value;
        }

        return $this->getFromLocalArray(
            $attribute,
            $lowerRule,
            $this->fallbackMessages
        ) ?: $key;
    }

    protected function getValidationTranslationKey(string $key): string
    {
        return implode(
            ModuleServiceProvider::TRANSLATIONS_MODULE_SEPARATOR,
            [CoreModuleServiceProvider::MODULE_NAME, $key]
        );
    }
}
