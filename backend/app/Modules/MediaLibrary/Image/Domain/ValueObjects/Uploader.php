<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\ValueObjects;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidUploaderNameException;
use App\Shared\Domain\ValueObjects\Email;

final readonly class Uploader
{
    public const int NAME_MAX_LENGTH = 100;

    public string $name;

    /**
     * @throws InvalidUploaderNameException
     */
    public function __construct(
        string $name,
        public Email $email,
    ) {
        $name = trim($name);
        throw_if($name === '' || mb_strlen($name) > self::NAME_MAX_LENGTH, InvalidUploaderNameException::class, null, [
            'max' => self::NAME_MAX_LENGTH,
        ]);

        $this->name = $name;
    }
}
