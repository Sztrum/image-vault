<?php

declare(strict_types=1);

namespace Tests\Unit\Weather;

use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoWeatherProvider;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenMeteoWeatherProviderTest extends TestCase
{
    private MockInterface $log;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        $this->log = Log::spy();
        $this->travelTo(CarbonImmutable::parse('2026-10-12 14:37:00', 'Europe/Warsaw'));
    }

    public function test_it_returns_temperature_for_current_hour_in_warsaw(): void
    {
        Http::fake(['api.open-meteo.com/*' => Http::response($this->hourlyPayload([
            '2026-10-12T13:00' => 11.2,
            '2026-10-12T14:00' => 12.4,
            '2026-10-12T15:00' => 13.1,
        ]))]);

        $temperature = $this->provider()->currentTemperatureInKatowice();

        $this->assertNotNull($temperature);
        $this->assertSame(12.4, $temperature->celsius);
        $this->assertSame('2026-10-12 14:00:00', $temperature->measuredAt->format('Y-m-d H:i:s'));
        $this->assertSame('Europe/Warsaw', $temperature->measuredAt->getTimezone()->getName());
        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), 'timezone=Europe%2FWarsaw'));
    }

    public function test_it_returns_null_when_current_hour_is_missing(): void
    {
        Http::fake(['api.open-meteo.com/*' => Http::response($this->hourlyPayload([
            '2026-10-12T12:00' => 10.0,
            '2026-10-12T13:00' => 11.2,
        ]))]);

        $this->assertNull($this->provider()->currentTemperatureInKatowice());
        $this->log->shouldHaveReceived('warning')->once();
    }

    public function test_it_returns_null_on_timeout(): void
    {
        Http::fake(['api.open-meteo.com/*' => fn () => throw new ConnectionException('cURL error 28: Operation timed out')]);

        $this->assertNull($this->provider()->currentTemperatureInKatowice());
        $this->log->shouldHaveReceived('warning')->once();
    }

    /**
     * @return array<string, array{0: string|array<string, mixed>, 1: int}>
     */
    public static function invalidResponses(): array
    {
        return [
            'server error' => ['Internal Server Error', 500],
            'missing hourly data' => [['error' => true, 'reason' => 'Invalid coordinates'], 200],
        ];
    }

    /**
     * @param  string|array<string, mixed>  $body
     */
    #[DataProvider('invalidResponses')]
    public function test_it_returns_null_on_invalid_response(string|array $body, int $status): void
    {
        Http::fake(['api.open-meteo.com/*' => Http::response($body, $status)]);

        $this->assertNull($this->provider()->currentTemperatureInKatowice());
        $this->log->shouldHaveReceived('warning')->once();
    }

    private function provider(): OpenMeteoWeatherProvider
    {
        return $this->app->make(OpenMeteoWeatherProvider::class);
    }

    /**
     * @param  array<string, float>  $temperaturesByHour
     * @return array<string, mixed>
     */
    private function hourlyPayload(array $temperaturesByHour): array
    {
        return [
            'timezone' => 'Europe/Warsaw',
            'hourly' => [
                'time' => array_keys($temperaturesByHour),
                'temperature_2m' => array_values($temperaturesByHour),
            ],
        ];
    }
}
