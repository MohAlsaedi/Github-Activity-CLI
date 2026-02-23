<?php

declare(strict_types=1);

// ─── Input Validation ────────────────────────────────────────────────────────

if ($argc < 2 || trim($argv[1]) === '') {
    fwrite(STDERR, "Usage: php github_activity.php <username>\n");
    exit(1);
}

$username = trim($argv[1]);

// Basic validation: GitHub usernames are alphanumeric + hyphens, max 39 chars
if (!preg_match('/^[a-zA-Z0-9-]{1,39}$/', $username)) {
    fwrite(STDERR, "Error: Invalid GitHub username format.\n");
    exit(1);
}

// ─── HTTP Request ─────────────────────────────────────────────────────────────

$url = "https://api.github.com/users/{$username}/events";

$options = [
    'http' => [
        'method'        => 'GET',
        'header'        => "User-Agent: PHP-GitHub-CLI/1.0\r\nAccept: application/vnd.github+json\r\n",
        'ignore_errors' => true,
        'timeout'       => 10,
    ],
];

$context  = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    fwrite(STDERR, "Error: Could not reach the GitHub API. Check your internet connection.\n");
    exit(1);
}

// ─── Parse Status Code ────────────────────────────────────────────────────────

$statusCode = parseStatusCode($http_response_header);

// ─── Handle Response ──────────────────────────────────────────────────────────

switch ($statusCode) {
    case 200:
        break; // All good, continue below

    case 404:
        fwrite(STDERR, "Error: Username '{$username}' not found on GitHub.\n");
        exit(1);

    case 403:
        fwrite(STDERR, "Error: API rate limit exceeded. Please wait a moment and try again.\n");
        exit(1);

    case 401:
        fwrite(STDERR, "Error: Unauthorized. Check your credentials.\n");
        exit(1);

    case 500:
    case 502:
    case 503:
        fwrite(STDERR, "Error: GitHub is having server issues (HTTP {$statusCode}). Try again later.\n");
        exit(1);

    default:
        fwrite(STDERR, "Error: Unexpected HTTP response: {$statusCode}.\n");
        exit(1);
}

// ─── Decode JSON ──────────────────────────────────────────────────────────────

$events = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    fwrite(STDERR, "Error: Failed to parse GitHub response: " . json_last_error_msg() . "\n");
    exit(1);
}

if (empty($events)) {
    echo "No recent public activity found for '{$username}'.\n";
    exit(0);
}

// ─── Display Results ──────────────────────────────────────────────────────────

echo str_pad("Event Type", 30) . str_pad("Repository", 40) . "Date" . "\n";
echo str_repeat("-", 85) . "\n";

foreach ($events as $event) {
    $type    = $event['type']              ?? 'Unknown';
    $repo    = $event['repo']['name']      ?? 'Unknown';
    $date    = $event['created_at']        ?? 'Unknown';

    // Make the date more human-readable
    $date    = formatDate($date);

    echo str_pad($type, 30) . str_pad($repo, 40) . $date . "\n";
}

// ─── Helper Functions ─────────────────────────────────────────────────────────

function parseStatusCode(array $headers): int
{
    // $http_response_header[0] is always the status line e.g. "HTTP/1.1 200 OK"
    if (preg_match('{HTTP/\S+\s(\d{3})}', $headers[0] ?? '', $match)) {
        return (int) $match[1];
    }

    return 0;
}

function formatDate(string $isoDate): string
{
    // GitHub returns ISO 8601 e.g. "2024-03-15T10:22:00Z"
    $timestamp = strtotime($isoDate);

    return $timestamp !== false
        ? date('Y-m-d H:i', $timestamp) . ' UTC'
        : $isoDate;
}