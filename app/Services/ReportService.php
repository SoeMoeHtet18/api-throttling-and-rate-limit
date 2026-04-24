<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use Carbon\Carbon;

class ReportService
{
    public function create(?int $userId, ?string $ip): Report
    {
        return Report::create([
            'user_id' => $userId,
            'ip' => $ip,
            'status' => ReportStatus::QUEUED,
            'progress' => 0,
        ]);
    }

    public function markAsRunning(Report $report): Report
    {
        $report->forceFill([
            'status' => ReportStatus::RUNNING,
            'started_at' => Carbon::now(),
            'error' => null,
        ])->save();

        return $report;
    }

    public function updateProgress(Report $report, int $progress): Report
    {
        $report->forceFill(['progress' => $progress])->save();
        return $report;
    }

    public function markAsFinished(Report $report, array $result): Report
    {
        $report->forceFill([
            'status' => ReportStatus::FINISHED,
            'progress' => 100,
            'finished_at' => Carbon::now(),
            'result' => json_encode($result),
        ])->save();

        return $report;
    }

    public function markAsFailed(Report $report, string $error): Report
    {
        $report->forceFill([
            'status' => ReportStatus::FAILED,
            'finished_at' => Carbon::now(),
            'error' => $error,
        ])->save();

        return $report;
    }
}