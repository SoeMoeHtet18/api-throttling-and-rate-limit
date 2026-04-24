<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRepositoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner' => ['required', 'string', 'min:1', 'max:39', 'regex:/^[a-zA-Z0-9\-_]+$/'],
            'repo' => ['required', 'string', 'min:1', 'max:100', 'regex:/^[a-zA-Z0-9\-_.]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner.regex' => 'Owner name contains invalid characters. Use only letters, numbers, hyphens, and underscores.',
            'repo.regex' => 'Repository name contains invalid characters. Use only letters, numbers, hyphens, underscores, and dots.',
        ];
    }
}