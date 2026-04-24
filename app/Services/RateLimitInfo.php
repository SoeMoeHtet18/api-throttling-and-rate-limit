<?php

declare(strict_types=1);

namespace App\Services;

readonly class RateLimitInfo
{
    public function __construct(
        public int $limit,
        public int $remaining,
        public int $reset,
    ) {}

    public function toArray(): array
    {
        return [
            'limit' => $this->limit,
            'remaining' => $this->remaining,
            'reset' => $this->reset,
        ];
    }
}