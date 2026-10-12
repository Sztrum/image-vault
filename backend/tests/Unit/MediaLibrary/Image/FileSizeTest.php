<?php

declare(strict_types=1);

namespace Tests\Unit\MediaLibrary\Image;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\EmptyImageException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooLargeException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use Tests\TestCase;

class FileSizeTest extends TestCase
{
    public function test_it_accepts_exactly_five_megabytes(): void
    {
        $this->assertSame(5_242_880, (new FileSize(5_242_880))->bytes);
    }

    public function test_it_rejects_one_byte_over_five_megabytes(): void
    {
        $this->expectException(ImageTooLargeException::class);

        new FileSize(5_242_881);
    }

    public function test_it_rejects_empty_file(): void
    {
        $this->expectException(EmptyImageException::class);

        new FileSize(0);
    }
}
