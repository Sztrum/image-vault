<?php

declare(strict_types=1);

namespace Tests\Unit\Shared;

use App\Shared\Domain\Exceptions\InvalidEmailException;
use App\Shared\Domain\ValueObjects\Email;
use Tests\TestCase;

class EmailTest extends TestCase
{
    public function test_it_accepts_valid_address(): void
    {
        $this->assertSame('anna@example.com', (new Email(' anna@example.com '))->value);
    }

    public function test_it_rejects_invalid_address(): void
    {
        $this->expectException(InvalidEmailException::class);

        new Email('anna@');
    }
}
