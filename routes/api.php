<?php

use App\Http\Controllers\GitHubProxyController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('github/repos/{owner}/{repo}', [GitHubProxyController::class, 'repo'])
    ->middleware('throttle:github');

Route::post('reports/heavy', [ReportController::class, 'processHeavyReport'])
    ->middleware('throttle:strict-api');

Route::get('reports/{report}', [ReportController::class, 'show'])
    ->middleware('throttle:strict-api');
