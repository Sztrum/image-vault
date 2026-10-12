<?php

declare(strict_types=1);

namespace App\Core\Domain\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

/**
 * @property string $id
 */
abstract class Model extends BaseModel
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            // Keep an id assigned up front (e.g. files named after it before the insert).
            if ($model->getKey() === null) {
                $model->{$model->getKeyName()} = (string) Str::uuid7();
            }
        });
    }
}
