<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\GitHubRepoResource;
use App\Services\GitHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GitHubProxyController extends Controller
{
    public function __construct(
        private readonly GitHubService $gitHubService,
    ) {}

    public function repo(Request $request, string $owner, string $repo): JsonResponse
    {
        if (! preg_match('/^[a-zA-Z0-9\-_]+$/', $owner)) {
            return response()->json([
                'message' => 'Invalid owner name format.',
            ], 422);
        }

        if (! preg_match('/^[a-zA-Z0-9\-_.]+$/', $repo)) {
            return response()->json([
                'message' => 'Invalid repository name format.',
            ], 422);
        }

        $response = $this->gitHubService->getRepo($owner, $repo);

        return response()->json(
            $response->data,
            $response->statusCode,
            $response->getRateLimitHeaders()
        );
    }
}