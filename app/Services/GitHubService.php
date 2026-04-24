<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class GitHubService
{
    private const API_VERSION = '2022-11-28';
    private const ACCEPT_HEADER = 'application/vnd.github+json';

    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $token,
    ) {}

    public static function create(): self
    {
        return new self(
            baseUrl: rtrim((string) config('services.github.base_url', 'https://api.github.com'), '/'),
            token: (string) config('services.github.token', ''),
        );
    }

    public function getRepo(string $owner, string $repo): GitHubResponse
    {
        try {
            $http = Http::accept(self::ACCEPT_HEADER)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'Laravel'),
                    'X-GitHub-Api-Version' => self::API_VERSION,
                ]);

            if ($this->token !== '') {
                $http = $http->withToken($this->token);
            }

            $response = $http->get("{$this->baseUrl}/repos/{$owner}/{$repo}");

            return new GitHubResponse(
                data: $response->json(),
                statusCode: $response->status(),
                rateLimit: new RateLimitInfo(
                    limit: (int) $response->header('X-RateLimit-Limit'),
                    remaining: (int) $response->header('X-RateLimit-Remaining'),
                    reset: (int) $response->header('X-RateLimit-Reset'),
                ),
            );
        } catch (ConnectionException $e) {
            return new GitHubResponse(
                data: ['message' => 'Failed to connect to GitHub API.'],
                statusCode: 502,
                rateLimit: null,
                isConnectionError: true,
            );
        } catch (RequestException $e) {
            return new GitHubResponse(
                data: $e->response?->json() ?? ['message' => 'GitHub API request failed.'],
                statusCode: $e->response?->status() ?? 500,
                rateLimit: $this->extractRateLimitFromException($e),
            );
        }
    }

    private function extractRateLimitFromException(RequestException $e): ?RateLimitInfo
    {
        $response = $e->response;
        if (! $response) {
            return null;
        }

        return new RateLimitInfo(
            limit: (int) $response->header('X-RateLimit-Limit'),
            remaining: (int) $response->header('X-RateLimit-Remaining'),
            reset: (int) $response->header('X-RateLimit-Reset'),
        );
    }
}