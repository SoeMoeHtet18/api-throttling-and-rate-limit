<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Throttling Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-card: #1a1f35;
            --bg-input: #0d1421;
            --border-color: rgba(139, 92, 246, 0.2);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #8b5cf6;
            --accent-secondary: #a78bfa;
            --accent-glow: rgba(139, 92, 246, 0.4);
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        header {
            text-align: center;
            margin-bottom: 48px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .badge-row {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 500;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--accent-secondary);
        }

        .badge svg {
            width: 14px;
            height: 14px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .grid { grid-template-columns: 1fr 1fr; }
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 28px;
            transition: all 0.3s ease;
            animation: slideUp 0.5s ease-out backwards;
        }

        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.2s; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card:hover {
            border-color: var(--accent-primary);
            box-shadow: 0 0 30px var(--accent-glow);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-primary) 0%, #6d28d9 100%);
        }

        .card-icon svg {
            width: 20px;
            height: 20px;
            color: white;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .input-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        input::placeholder {
            color: var(--text-muted);
        }

        button {
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #6d28d9 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--accent-glow);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover:not(:disabled) {
            border-color: var(--accent-primary);
            background: rgba(139, 92, 246, 0.1);
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .output-section {
            margin-top: 20px;
        }

        .status-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            background: var(--bg-input);
            border-radius: 10px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .status-label {
            color: var(--text-muted);
        }

        .status-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            padding: 4px 8px;
            background: var(--bg-secondary);
            border-radius: 6px;
            color: var(--accent-secondary);
        }

        .pill {
            display: inline-block;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
        }

        .pill-success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .pill-warning {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .pill-error {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error);
        }

        .pill-info {
            background: rgba(139, 92, 246, 0.15);
            color: var(--accent-secondary);
        }

        pre {
            white-space: pre-wrap;
            word-break: break-word;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            line-height: 1.5;
            max-height: 300px;
            overflow: auto;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: var(--bg-input);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 16px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-primary) 0%, var(--success) 100%);
            border-radius: 999px;
            transition: width 0.3s ease;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .info-item {
            background: var(--bg-input);
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        .info-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-secondary);
        }

        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .code-block {
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
        }

        .code-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            color: var(--text-muted);
            font-size: 12px;
        }

        .code-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .code-dot.red { background: #ef4444; }
        .code-dot.yellow { background: #f59e0b; }
        .code-dot.green { background: #10b981; }

        .hidden { display: none; }

        footer {
            text-align: center;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 14px;
        }

        footer a {
            color: var(--accent-secondary);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="badge-row">
            <span class="badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                Laravel 13
            </span>
            <span class="badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                Redis Throttling
            </span>
            <span class="badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                Rate Limiting
            </span>
        </div>
        <h1>API Throttling Demo</h1>
        <p class="subtitle">Production-ready demonstration of request throttling and queue-based work throttling patterns in Laravel</p>
    </header>

    <div class="grid">
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 00-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0020 4.77 5.07 5.07 0 0019.91 1S18.73.65 16 2.48a13.38 13.38 0 00-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 005 4.77a5.44 5.44 0 00-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 009 18.13V22"/></svg>
                </div>
                <div>
                    <h3 class="card-title">GitHub Proxy</h3>
                    <span class="pill pill-info">HTTP Throttling</span>
                </div>
            </div>
            <p class="card-desc">Fetch GitHub repository info through a rate-limited proxy. Try clicking "Fetch" rapidly to trigger HTTP 429.</p>

            <div class="input-group">
                <input id="owner" value="laravel" placeholder="Owner (e.g. laravel)" />
                <input id="repo" value="framework" placeholder="Repo (e.g. framework)" />
            </div>

            <button id="fetch" class="btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                Fetch Repository
            </button>

            <div id="githubOutput" class="output-section hidden">
                <div class="status-bar">
                    <div class="status-item">
                        <span class="status-label">Status:</span>
                        <span id="statusBadge" class="pill"></span>
                    </div>
                    <div class="status-item hidden" id="ratelimitSection">
                        <span class="status-label">Rate Limit:</span>
                        <span id="ratelimitValue" class="status-value"></span>
                    </div>
                </div>

                <div class="info-grid hidden" id="repoInfo">
                    <div class="info-item">
                        <div class="info-value" id="stars">-</div>
                        <div class="info-label">Stars</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value" id="forks">-</div>
                        <div class="info-label">Forks</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value" id="issues">-</div>
                        <div class="info-label">Open Issues</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value" id="watchers">-</div>
                        <div class="info-label">Watchers</div>
                    </div>
                </div>

                <div class="code-block">
                    <div class="code-header">
                        <span class="code-dot red"></span>
                        <span class="code-dot yellow"></span>
                        <span class="code-dot green"></span>
                        <span>Response JSON</span>
                    </div>
                    <pre id="githubJson"></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                </div>
                <div>
                    <h3 class="card-title">Report Generator</h3>
                    <span class="pill pill-success">Queue Throttling</span>
                </div>
            </div>
            <p class="card-desc">Generate heavy reports via Redis-throttled queue workers. Accepts requests immediately, processes in background.</p>

            <div class="btn-row">
                <button id="startReport" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Start Report
                </button>
                <div style="display: flex; gap: 8px; flex: 1;">
                    <input id="reportIdInput" type="number" placeholder="Report ID" style="flex: 1; min-width: 80px;" />
                    <button id="checkReport" class="btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Check
                    </button>
                </div>
            </div>

            <div id="reportOutput" class="output-section hidden">
                <div class="status-bar">
                    <div class="status-item">
                        <span class="status-label">Report:</span>
                        <span id="reportIdBadge" class="status-value"></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Status:</span>
                        <span id="reportStatusBadge" class="pill"></span>
                    </div>
                </div>

                <div class="progress-bar">
                    <div id="reportProgress" class="progress-fill" style="width: 0%"></div>
                </div>
                <p id="progressText" style="font-size: 12px; color: var(--text-muted); margin-top: 8px; text-align: center;"></p>

                <div class="code-block">
                    <div class="code-header">
                        <span class="code-dot red"></span>
                        <span class="code-dot yellow"></span>
                        <span class="code-dot green"></span>
                        <span>Report Data</span>
                    </div>
                    <pre id="reportJson"></pre>
                </div>
            </div>
        </div>

        <div class="card" style="grid-column: 1 / -1;">
            <div class="card-header">
                <div class="card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                </div>
                <div>
                    <h3 class="card-title">How It Works</h3>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
                <div>
                    <h4 style="color: var(--accent-secondary); margin-bottom: 8px;">Request Throttling</h4>
                    <p style="color: var(--text-secondary); font-size: 14px;">Laravel's rate limiter blocks clients exceeding request limits with HTTP 429 responses. Configure limits per endpoint.</p>
                    <div class="code-block" style="margin-top: 12px;">
                        <pre style="font-size: 11px;">RateLimiter::for('github', function (Request $request) {
    return Limit::perMinute(10)
        ->by($request->ip());
});</pre>
                    </div>
                </div>
                <div>
                    <h4 style="color: var(--success); margin-bottom: 8px;">Queue Throttling</h4>
                    <p style="color: var(--text-secondary); font-size: 14px;">Server accepts requests immediately, dispatches jobs, and Redis paces background work to prevent overload.</p>
                    <div class="code-block" style="margin-top: 12px;">
                        <pre style="font-size: 11px;">Redis::throttle('report-gen')
    ->allow(5)
    ->every(60)
    ->then(fn() => process(),
           fn() => release(10));</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>Built with <a href="https://laravel.com" target="_blank">Laravel</a> &middot; Demo project showcasing production patterns</p>
    </footer>
</div>

<script>
const $ = id => document.getElementById(id);

function setBusy(btn, busy, text) {
    btn.disabled = busy;
    btn.innerHTML = busy
        ? '<span class="spinner"></span> ' + text
        : text;
}

function setStatus(text, type) {
    const map = {
        success: 'pill-success',
        warning: 'pill-warning',
        error: 'pill-error',
        info: 'pill-info'
    };
    return `<span class="pill ${map[type] || ''}">${text}</span>`;
}

$('fetch').addEventListener('click', async () => {
    const owner = $('owner').value.trim();
    const repo = $('repo').value.trim();

    if (!owner || !repo) {
        alert('Owner and repo are required');
        return;
    }

    const btn = $('fetch');
    setBusy(btn, true, 'Fetching...');
    $('githubOutput').classList.add('hidden');

    try {
        const res = await fetch(`/api/github/repos/${encodeURIComponent(owner)}/${encodeURIComponent(repo)}`);
        const json = await res.json().catch(() => ({}));

        const statusType = res.ok ? (res.status === 200 ? 'success' : 'info') : 'error';
        const statusBadge = $('statusBadge');
        statusBadge.textContent = res.status;
        statusBadge.className = `pill pill-${statusType}`;

        const limit = res.headers.get('X-RateLimit-Remaining');
        const reset = res.headers.get('X-RateLimit-Reset');
        if (limit) {
            $('ratelimitValue').textContent = `${limit} / min`;
            $('ratelimitSection').classList.remove('hidden');
        }

        if (json.data?.name || json.name) {
            const repoData = json.data || json;
            $('stars').textContent = (repoData.stargazers_count || 0).toLocaleString();
            $('forks').textContent = (repoData.forks_count || 0).toLocaleString();
            $('issues').textContent = (repoData.open_issues_count || 0).toLocaleString();
            $('watchers').textContent = (repoData.watchers_count || 0).toLocaleString();
            $('repoInfo').classList.remove('hidden');
            $('githubJson').textContent = JSON.stringify(json, null, 2);
        } else {
            $('repoInfo').classList.add('hidden');
            $('githubJson').textContent = JSON.stringify(json, null, 2);
        }
        $('githubOutput').classList.remove('hidden');
    } catch (e) {
        const statusBadge = $('statusBadge');
        statusBadge.textContent = 'Error';
        statusBadge.className = 'pill pill-error';
        $('githubJson').textContent = JSON.stringify({ error: 'Request failed' }, null, 2);
        $('githubOutput').classList.remove('hidden');
    } finally {
        setBusy(btn, false, `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg> Fetch Repository`);
    }
});

let currentReportId = null;
let pollTimer = null;

$('startReport').addEventListener('click', async () => {
    if (pollTimer) clearInterval(pollTimer);
    currentReportId = null;
    $('reportOutput').classList.add('hidden');
    $('reportIdBadge').textContent = '-';
    $('reportProgress').style.width = '0%';
    $('progressText').textContent = '';

    const btn = $('startReport');
    setBusy(btn, true, 'Starting...');

    try {
        const res = await fetch('/api/reports/heavy', { method: 'POST' });
        const json = await res.json().catch(() => ({}));

        if (json.report_id) {
            currentReportId = json.report_id;
            $('reportIdBadge').textContent = `#${currentReportId}`;
            $('reportIdInput').value = currentReportId;
            $('reportOutput').classList.remove('hidden');
            await fetchReportStatus();

            pollTimer = setInterval(async () => {
                const status = await fetchReportStatus();
                if (status?.is_terminal) {
                    clearInterval(pollTimer);
                }
            }, 1500);
        } else {
            $('reportOutput').classList.add('hidden');
        }
    } catch (e) {
        console.error(e);
        $('reportOutput').classList.add('hidden');
    } finally {
        setBusy(btn, false, `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg> Start Report`);
    }
});

$('checkReport').addEventListener('click', async () => {
    const inputId = $('reportIdInput').value.trim();
    if (!inputId) {
        alert('Please enter a Report ID');
        return;
    }
    if (pollTimer) clearInterval(pollTimer);
    currentReportId = parseInt(inputId);
    $('reportIdBadge').textContent = `#${currentReportId}`;
    $('reportOutput').classList.remove('hidden');
    await fetchReportStatus();
    $('reportIdInput').value = currentReportId;
});

async function fetchReportStatus() {
    if (!currentReportId) return;

    try {
        const res = await fetch(`/api/reports/${currentReportId}`);
        const json = await res.json().catch(() => ({}));
        const data = json.data || json;
        const status = data.status;

        const statusMap = {
            queued: ['Queued', 'info'],
            running: ['Running', 'warning'],
            finished: ['Completed', 'success'],
            failed: ['Failed', 'error']
        };
        const [text, type] = statusMap[status?.value] || ['Unknown', 'info'];

        const badge = $('reportStatusBadge');
        if (badge) {
            badge.textContent = text;
            badge.className = `pill pill-${type}`;
        }

        const progress = data.progress || 0;
        $('reportProgress').style.width = `${progress}%`;
        $('progressText').textContent = status?.is_terminal
            ? `Finished at ${progress}%`
            : `Processing... ${progress}%`;

        $('reportJson').textContent = JSON.stringify(data, null, 2);

        return status;
    } catch (e) {
        console.error(e);
    }
}
</script>
</body>
</html>