<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class GitHubApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 500,
        public readonly array $context = [],
    ) {
        parent::__construct($message, $statusCode);
    }

    public static function connectionFailed(): self
    {
        return new self('Failed to connect to GitHub API.', 502);
    }

    public static function rateLimitExceeded(int $resetTime): self
    {
        return new self(
            'GitHub API rate limit exceeded.',
            429,
            ['reset_at' => $resetTime]
        );
    }

    public static function notFound(string $owner, string $repo): self
    {
        return new self(
            "Repository '{$owner}/{$repo}' not found.",
            404
        );
    }

    public static function unauthorized(): self
    {
        return new self('Invalid or missing GitHub token.', 401);
    }

    public static function serverError(): self
    {
        return new self('GitHub API server error.', 503);
    }
}