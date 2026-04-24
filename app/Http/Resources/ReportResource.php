<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\ReportStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $report = $this->resource;
        $status = $report->status instanceof ReportStatus
            ? $report->status
            : ReportStatus::tryFrom((string) $report->status);

        $statusValue = $status?->value ?? (string) $report->status;

        $data = [
            'id' => (int) $report->id,
            'status' => [
                'value' => $statusValue,
                'label' => $status?->label() ?? $statusValue,
                'is_terminal' => $status?->isTerminal() ?? false,
            ],
            'progress' => (int) $report->progress,
            'timestamps' => [
                'started_at' => $report->started_at?->toISOString(),
                'finished_at' => $report->finished_at?->toISOString(),
                'created_at' => $report->created_at->toISOString(),
            ],
            'metadata' => [
                'user_id' => $report->user_id,
            ],
        ];

        if ($status === ReportStatus::FINISHED && $report->result) {
            $data['result'] = json_decode($report->result, true);
        }

        if ($status === ReportStatus::FAILED && $report->error) {
            $data['error'] = $report->error;
        }

        return $data;
    }
}