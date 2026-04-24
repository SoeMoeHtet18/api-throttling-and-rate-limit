<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip',
        'status',
        'progress',
        'started_at',
        'finished_at',
        'result',
        'error',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'status' => ReportStatus::class,
    ];

    protected $hidden = [
        'ip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isQueued(): bool
    {
        return $this->status === ReportStatus::QUEUED;
    }

    public function isRunning(): bool
    {
        return $this->status === ReportStatus::RUNNING;
    }

    public function isFinished(): bool
    {
        return $this->status === ReportStatus::FINISHED;
    }

    public function isFailed(): bool
    {
        return $this->status === ReportStatus::FAILED;
    }

    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }
}