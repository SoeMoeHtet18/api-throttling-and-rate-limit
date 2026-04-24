<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    public function processHeavyReport(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $report = $this->reportService->create($userId, $request->ip());

        GenerateReportJob::dispatch($report->id);

        return response()->json([
            'message' => 'Your report is being processed.',
            'report_id' => $report->id,
        ], 202);
    }

    public function show(Request $request, string $reportId): ReportResource
    {
        $report = Report::findOrFail((int) $reportId);

        return new ReportResource($report);
    }
}