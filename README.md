# API Throttling Laravel Demo

A production-ready demonstration of API rate limiting patterns in Laravel, featuring request throttling and queue-based work throttling with Redis.

![Laravel](https://img.shields.io/badge/Laravel-13.0-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)
![Redis](https://img.shields.io/badge/Redis-7.0+-orange.svg)
![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen.svg)

## Overview

This project demonstrates two distinct but complementary rate-limiting patterns:

1. **Request Throttling** - Laravel blocks clients exceeding request limits with HTTP 429 responses
2. **Work Throttling** - Redis paces background job execution while accepting requests immediately

## Features

- **Clean Architecture** - Service classes, API Resources, Enums, Form Requests
- **Rate Limiting** - Configurable per-endpoint limits with custom responses
- **Queue Processing** - Redis-throttled job processing with progress tracking
- **API Resources** - Consistent JSON responses via Laravel API Resources
- **Validation** - Form Request validation with custom error messages
- **Error Handling** - Custom exception handling with consistent API errors
- **Type Safety** - PHP 8.3 strict types throughout
- **Full Test Coverage** - Feature and unit tests included

## Tech Stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 13.0 |
| PHP | 8.3+ |
| Cache | Database / Redis |
| Queue | Database / Redis |
| Redis | phpredis |

## API Endpoints

### Request Throttling Endpoints

| Method | Endpoint | Rate Limit | Description |
|--------|----------|------------|-------------|
| GET | `/api/github/repos/{owner}/{repo}` | Configurable (default 10/min) | GitHub repo proxy |
| POST | `/api/reports/heavy` | 60/min | Create heavy report |
| GET | `/api/reports/{id}` | 60/min | Get report status |

### Response Format

```json
{
  "id": 1,
  "status": {
    "value": "queued",
    "label": "Queued",
    "is_terminal": false
  },
  "progress": 0,
  "timestamps": {
    "started_at": null,
    "finished_at": null,
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

## Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/api-throttling-laravel.git
cd api-throttling-laravel

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start the server
php artisan serve
```

## Configuration

### Environment Variables

```env
# Optional: GitHub API token for higher rate limits
GITHUB_TOKEN=your_github_token

# Optional: Override the throttle limit for GitHub proxy endpoint (default: 10)
GITHUB_THROTTLE_PER_MINUTE=10

# Queue connection (database or redis)
QUEUE_CONNECTION=redis

# Redis configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Usage

### Start Redis (required for job throttling)

```bash
# Using Docker
docker run -d -p 6379:6379 redis:7-alpine

# Or install Redis locally
brew install redis
brew services start redis
```

### Run the Application

```bash
# Start the web server
php artisan serve

# Start the queue worker (in a separate terminal)
php artisan queue:work
```

### Interactive Demo

Visit `http://127.0.0.1:8000` for an interactive demo UI that lets you:

1. Fetch GitHub repositories and trigger rate limits
2. Create reports and monitor progress via polling

### API Examples

```bash
# Fetch a GitHub repository
curl http://127.0.0.1:8000/api/github/repos/laravel/framework

# Create a heavy report
curl -X POST http://127.0.0.1:8000/api/reports/heavy

# Check report status
curl http://127.0.0.1:8000/api/reports/1
```

## Architecture

```
app/
├── Enums/
│   └── ReportStatus.php           # Report status enum
├── Exceptions/
│   ├── GitHubApiException.php     # GitHub API exceptions
│   └── ReportGenerationException.php
├── Http/
│   ├── Controllers/
│   │   ├── GitHubProxyController.php
│   │   └── ReportController.php
│   ├── Requests/
│   │   └── GetRepositoryRequest.php
│   └── Resources/
│       ├── GitHubRepoResource.php
│       └── ReportResource.php
├── Jobs/
│   └── GenerateReportJob.php      # Redis-throttled job
├── Models/
│   └── Report.php
├── Providers/
│   └── AppServiceProvider.php     # Rate limiter config
└── Services/
    ├── GitHubService.php
    ├── GitHubResponse.php
    ├── RateLimitInfo.php
    └── ReportService.php
```

## Rate Limiting Patterns

### HTTP Request Throttling

```php
RateLimiter::for('github', function (Request $request) {
    $maxPerMinute = config('services.github.throttle_per_minute', 10);

    return Limit::perMinute($maxPerMinute)
        ->by($request->ip())
        ->response(fn() => response()->json([
            'message' => 'Too many requests.',
            'retry_after' => /* ... */,
        ], 429));
});
```

### Queue + Redis Throttling

```php
Redis::throttle('report-generation')
    ->allow(5)
    ->every(60)
    ->block(0)
    ->then(
        fn() => $this->processReport(),
        fn() => $this->release(10),  // Retry after 10 seconds
    );
```

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=GitHubThrottleTest
```

## Code Quality

```bash
# Static analysis (requires phpstan)
./vendor/bin/phpstan analyse

# Fix code style (requires laravel/pint)
./vendor/bin/pint
```

## Requirements Checklist

This project demonstrates proficiency in:

- [x] Laravel Framework
- [x] PHP 8.3+ Features (Enums, Readonly, Attributes)
- [x] RESTful API Design
- [x] Service Layer Pattern
- [x] Queue Jobs with Redis
- [x] Rate Limiting
- [x] API Resources
- [x] Form Request Validation
- [x] Custom Exception Handling
- [x] Unit & Feature Testing
- [x] GitHub Actions CI/CD
- [x] Type Safety (strict_types)

## License

MIT License