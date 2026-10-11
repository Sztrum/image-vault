<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    public function test_it_returns_healthy_status(): void
    {
        $this->get('/api/v1/health')
            ->assertOk()
            ->assertExactJson([
                'status' => 200,
                'data' => [
                    'healthy' => true,
                ],
            ]);
    }

    public function test_route_is_named_within_api_namespace(): void
    {
        $this->assertSame(url('/api/v1/health'), route('api.core.health'));
    }

    public function test_it_returns_json_error_for_unknown_route(): void
    {
        $this->get('/api/v1/unknown')
            ->assertNotFound()
            ->assertJson([
                'status' => 404,
                'message' => 'Nie znaleziono',
            ]);
    }

    public function test_it_allows_cors_requests_from_frontend_url(): void
    {
        config(['cors.allowed_origins' => ['http://frontend.test']]);

        $this->get('/api/v1/health', ['Origin' => 'http://frontend.test'])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://frontend.test');
    }
}
