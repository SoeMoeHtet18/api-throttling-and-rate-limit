<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class GitHubThrottleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('github');
        RateLimiter::clear('strict-api');
    }

    public function test_github_endpoint_is_throttled_per_ip(): void
    {
        config()->set('services.github.throttle_per_minute', 3);

        Http::fake([
            'api.github.com/repos/*' => Http::response(['ok' => true], 200),
        ]);

        $server = ['REMOTE_ADDR' => '203.0.113.10'];

        $this->withServerVariables($server)->getJson('/api/github/repos/laravel/framework')->assertOk();
        $this->withServerVariables($server)->getJson('/api/github/repos/laravel/framework')->assertOk();
        $this->withServerVariables($server)->getJson('/api/github/repos/laravel/framework')->assertOk();

        $this->withServerVariables($server)->getJson('/api/github/repos/laravel/framework')->assertStatus(429);
    }

    public function test_github_endpoint_returns_rate_limit_headers(): void
    {
        Http::fake([
            'api.github.com/repos/*' => function () {
                return Http::response(['name' => 'framework'], 200, [
                    'X-RateLimit-Remaining' => '59',
                    'X-RateLimit-Reset' => '1234567890',
                ]);
            },
        ]);

        $response = $this->getJson('/api/github/repos/laravel/framework');

        $response->assertOk()
            ->assertHeader('X-RateLimit-Remaining')
            ->assertHeader('X-RateLimit-Reset');
    }

    public function test_github_endpoint_handles_not_found(): void
    {
        Http::fake([
            'api.github.com/repos/*' => Http::response(['message' => 'Not Found'], 404),
        ]);

        $this->getJson('/api/github/repos/nonexistent-owner/nonexistent-repo')
            ->assertStatus(404)
            ->assertJson(['message' => 'Not Found']);
    }

    public function test_github_endpoint_validates_owner_format(): void
    {
        Http::fake([
            'api.github.com/repos/*' => Http::response(['ok' => true], 200),
        ]);

        $this->getJson('/api/github/repos/invalid@owner/framework')
            ->assertStatus(422)
            ->assertJson(['message' => 'Invalid owner name format.']);
    }

    public function test_github_endpoint_validates_repo_format(): void
    {
        Http::fake([
            'api.github.com/repos/*' => Http::response(['ok' => true], 200),
        ]);

        $this->getJson('/api/github/repos/laravel/invalid@repo')
            ->assertStatus(422)
            ->assertJson(['message' => 'Invalid repository name format.']);
    }
}