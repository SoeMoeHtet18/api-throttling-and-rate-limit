<?php

declare(strict_types=1);

namespace App\Services;

readonly class GitHubResponse
{
    public function __construct(
        public array $data,
        public int $statusCode,
        public ?RateLimitInfo $rateLimit,
        public bool $isConnectionError = false,
    ) {}

    public function getRateLimitHeaders(): array
    {
        if (! $this->rateLimit) {
            return [];
        }

        return [
            'X-RateLimit-Limit' => (string) $this->rateLimit->limit,
            'X-RateLimit-Remaining' => (string) $this->rateLimit->remaining,
            'X-RateLimit-Reset' => (string) $this->rateLimit->reset,
        ];
    }
}