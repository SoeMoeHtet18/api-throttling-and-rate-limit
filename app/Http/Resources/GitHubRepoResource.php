<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GitHubRepoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'] ?? null,
            'name' => $this->resource['name'] ?? null,
            'full_name' => $this->resource['full_name'] ?? null,
            'description' => $this->resource['description'] ?? null,
            'html_url' => $this->resource['html_url'] ?? null,
            'stargazers_count' => $this->resource['stargazers_count'] ?? 0,
            'forks_count' => $this->resource['forks_count'] ?? 0,
            'open_issues_count' => $this->resource['open_issues_count'] ?? 0,
            'watchers_count' => $this->resource['watchers_count'] ?? 0,
            'language' => $this->resource['language'] ?? null,
            'visibility' => $this->resource['visibility'] ?? null,
            'default_branch' => $this->resource['default_branch'] ?? null,
            'created_at' => $this->resource['created_at'] ?? null,
            'updated_at' => $this->resource['updated_at'] ?? null,
            'pushed_at' => $this->resource['pushed_at'] ?? null,
            'topics' => $this->resource['topics'] ?? [],
            'license' => $this->resource['license'] ?? null,
            'owner' => [
                'login' => $this->resource['owner']['login'] ?? null,
                'avatar_url' => $this->resource['owner']['avatar_url'] ?? null,
                'html_url' => $this->resource['owner']['html_url'] ?? null,
            ],
        ];
    }
}