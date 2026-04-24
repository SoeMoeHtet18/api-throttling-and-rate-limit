<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ReportGenerationException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $reportId,
        public readonly array $context = [],
    ) {
        parent::__construct($message);
    }

    public static function notFound(int $reportId): self
    {
        return new self("Report #{$reportId} not found.", $reportId);
    }

    public static function generationFailed(int $reportId, string $reason): self
    {
        return new self("Report #{$reportId} generation failed: {$reason}", $reportId, [
            'reason' => $reason,
        ]);
    }
}