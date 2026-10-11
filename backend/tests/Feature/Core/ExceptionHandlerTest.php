<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Domain\Exceptions\DomainException;
use App\Core\Domain\Exceptions\HttpException;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('api')->group(function (): void {
            Route::get('/api/v1/test/runtime', fn () => throw new RuntimeException('SQLSTATE[42S02]: Table image_vault.images missing', 404));
            Route::get('/api/v1/test/http-500', fn () => throw new HttpException('Internal handler App\Foo missing'));
            Route::get('/api/v1/test/domain', fn () => throw new DomainException('core::domain.model_not_found'));
        });
    }

    public function test_it_hides_unexpected_exception_details_when_debug_is_disabled(): void
    {
        config(['app.debug' => false]);

        $this->getJson('/api/v1/test/runtime')
            ->assertInternalServerError()
            ->assertExactJson([
                'status' => 500,
                'message' => 'Wystąpił błąd serwera. Spróbuj ponownie później.',
            ]);
    }

    public function test_it_hides_server_http_exception_message_when_debug_is_disabled(): void
    {
        config(['app.debug' => false]);

        $this->getJson('/api/v1/test/http-500')
            ->assertInternalServerError()
            ->assertExactJson([
                'status' => 500,
                'message' => 'Wystąpił błąd serwera. Spróbuj ponownie później.',
            ]);
    }

    public function test_it_keeps_translated_domain_exception_message_when_debug_is_disabled(): void
    {
        config(['app.debug' => false]);

        $this->getJson('/api/v1/test/domain')
            ->assertUnprocessable()
            ->assertExactJson([
                'status' => 422,
                'message' => 'Nie znaleziono',
            ]);
    }

    public function test_it_exposes_exception_details_when_debug_is_enabled(): void
    {
        config(['app.debug' => true]);

        $this->getJson('/api/v1/test/runtime')
            ->assertInternalServerError()
            ->assertJsonPath('message', 'SQLSTATE[42S02]: Table image_vault.images missing')
            ->assertJsonPath('data.exception', RuntimeException::class);
    }
}
