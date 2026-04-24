<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $maxExceptions = 1;
    public int $timeout = 300;
    public int $backoff = 10;

    public function __construct(
        public readonly int $reportId,
    ) {}

    public function handle(ReportService $reportService): void
    {
        $report = Report::find($this->reportId);

        if (! $report) {
            Log::warning("Report #{$this->reportId} not found, skipping job.");
            return;
        }

        Redis::throttle('report-generation')
            ->allow(5)
            ->every(60)
            ->block(0)
            ->then(
                fn() => $this->processReport($report, $reportService),
                fn() => $this->releaseJob(),
            );
    }

    private function processReport(Report $report, ReportService $reportService): void
    {
        $reportService->markAsRunning($report);

        try {
            foreach ([20, 45, 70, 100] as $progress) {
                usleep(250_000);
                $reportService->updateProgress($report, $progress);
            }

            $reportService->markAsFinished($report, [
                'summary' => 'Demo report generated successfully.',
                'generated_at' => now()->toISOString(),
                'total_items_processed' => rand(100, 1000),
            ]);
        } catch (\Throwable $e) {
            $reportService->markAsFailed($report, $e->getMessage());
            throw $e;
        }
    }

    private function releaseJob(): void
    {
        Log::info("Report #{$this->reportId} throttled, releasing back to queue.");
        $this->release(10);
    }

    public function failed(\Throwable $exception): void
    {
        $report = Report::find($this->reportId);

        if ($report) {
            $report->forceFill([
                'status' => ReportStatus::FAILED,
                'error' => $exception->getMessage(),
                'finished_at' => now(),
            ])->save();
        }

        Log::error("Report #{$this->reportId} job failed permanently.", [
            'exception' => $exception->getMessage(),
        ]);
    }
}