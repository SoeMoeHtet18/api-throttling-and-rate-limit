<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('github');
        RateLimiter::clear('strict-api');
    }

    public function test_heavy_report_endpoint_creates_report_and_dispatches_job(): void
    {
        Queue::fake();

        $res = $this->postJson('/api/reports/heavy');
        $res->assertStatus(202)->assertJsonStructure(['message', 'report_id']);

        $reportId = $res->json('report_id');

        $this->assertDatabaseHas('reports', [
            'id' => $reportId,
            'status' => ReportStatus::QUEUED->value,
            'progress' => 0,
        ]);

        Queue::assertPushed(GenerateReportJob::class, function (GenerateReportJob $job) use ($reportId) {
            return $job->reportId === $reportId;
        });
    }

    public function test_report_status_endpoint_returns_report_resource(): void
    {
        $report = Report::create([
            'user_id' => null,
            'ip' => '203.0.113.10',
            'status' => ReportStatus::QUEUED,
            'progress' => 0,
        ]);

        $response = $this->getJson("/api/reports/{$report->id}");

        $response->assertOk();
        $json = $response->json('data');
        $this->assertEquals($report->id, $json['id']);
        $this->assertEquals(ReportStatus::QUEUED->value, $json['status']['value']);
        $this->assertEquals(false, $json['status']['is_terminal']);
        $this->assertEquals(0, $json['progress']);
    }

    public function test_finished_report_includes_result(): void
    {
        $report = Report::create([
            'user_id' => null,
            'ip' => '203.0.113.10',
            'status' => ReportStatus::FINISHED,
            'progress' => 100,
            'finished_at' => now(),
            'result' => json_encode(['summary' => 'Completed.']),
        ]);

        $response = $this->getJson("/api/reports/{$report->id}");

        $response->assertOk();
        $json = $response->json('data');
        $this->assertEquals(ReportStatus::FINISHED->value, $json['status']['value']);
        $this->assertEquals('Completed.', $json['result']['summary']);
    }

    public function test_failed_report_includes_error(): void
    {
        $report = Report::create([
            'user_id' => null,
            'ip' => '203.0.113.10',
            'status' => ReportStatus::FAILED,
            'progress' => 45,
            'finished_at' => now(),
            'error' => 'Something went wrong.',
        ]);

        $response = $this->getJson("/api/reports/{$report->id}");

        $response->assertOk();
        $json = $response->json('data');
        $this->assertEquals(ReportStatus::FAILED->value, $json['status']['value']);
        $this->assertEquals('Something went wrong.', $json['error']);
    }

    public function test_report_not_found_returns_404(): void
    {
        $this->getJson('/api/reports/999999')
            ->assertStatus(404)
            ->assertJson(['message' => 'Resource not found.']);
    }

    public function test_report_service_creates_report(): void
    {
        $service = app(ReportService::class);

        $report = $service->create(userId: null, ip: '127.0.0.1');

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals(ReportStatus::QUEUED, $report->status);
        $this->assertEquals(0, $report->progress);
    }

    public function test_report_service_marks_as_running(): void
    {
        $report = Report::create([
            'user_id' => null,
            'ip' => '127.0.0.1',
            'status' => ReportStatus::QUEUED,
            'progress' => 0,
        ]);

        $service = app(ReportService::class);
        $updated = $service->markAsRunning($report);

        $this->assertEquals(ReportStatus::RUNNING, $updated->status);
        $this->assertNotNull($updated->started_at);
    }

    public function test_report_service_marks_as_finished(): void
    {
        $report = Report::create([
            'user_id' => null,
            'ip' => '127.0.0.1',
            'status' => ReportStatus::RUNNING,
            'progress' => 70,
        ]);

        $service = app(ReportService::class);
        $updated = $service->markAsFinished($report, ['items' => 100]);

        $this->assertEquals(ReportStatus::FINISHED, $updated->status);
        $this->assertEquals(100, $updated->progress);
        $this->assertNotNull($updated->finished_at);
        $this->assertEquals(['items' => 100], json_decode($updated->result, true));
    }

    public function test_report_service_marks_as_failed(): void
    {
        $report = Report::create([
            'user_id' => null,
            'ip' => '127.0.0.1',
            'status' => ReportStatus::RUNNING,
            'progress' => 50,
        ]);

        $service = app(ReportService::class);
        $updated = $service->markAsFailed($report, 'Database connection failed.');

        $this->assertEquals(ReportStatus::FAILED, $updated->status);
        $this->assertEquals('Database connection failed.', $updated->error);
        $this->assertNotNull($updated->finished_at);
    }
}