<?php

namespace Tests\Unit;

use App\Enums\ReportStatus;
use PHPUnit\Framework\TestCase;

class ReportStatusEnumTest extends TestCase
{
    public function test_queued_status_label(): void
    {
        $this->assertEquals('Queued', ReportStatus::QUEUED->label());
    }

    public function test_running_status_label(): void
    {
        $this->assertEquals('Running', ReportStatus::RUNNING->label());
    }

    public function test_finished_status_label(): void
    {
        $this->assertEquals('Completed', ReportStatus::FINISHED->label());
    }

    public function test_failed_status_label(): void
    {
        $this->assertEquals('Failed', ReportStatus::FAILED->label());
    }

    public function test_terminal_statuses(): void
    {
        $this->assertTrue(ReportStatus::FINISHED->isTerminal());
        $this->assertTrue(ReportStatus::FAILED->isTerminal());
        $this->assertFalse(ReportStatus::QUEUED->isTerminal());
        $this->assertFalse(ReportStatus::RUNNING->isTerminal());
    }

    public function test_status_values(): void
    {
        $this->assertEquals('queued', ReportStatus::QUEUED->value);
        $this->assertEquals('running', ReportStatus::RUNNING->value);
        $this->assertEquals('finished', ReportStatus::FINISHED->value);
        $this->assertEquals('failed', ReportStatus::FAILED->value);
    }
}