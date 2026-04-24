<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportStatus: string
{
    case QUEUED = 'queued';
    case RUNNING = 'running';
    case FINISHED = 'finished';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => 'Queued',
            self::RUNNING => 'Running',
            self::FINISHED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::FINISHED || $this === self::FAILED;
    }
}