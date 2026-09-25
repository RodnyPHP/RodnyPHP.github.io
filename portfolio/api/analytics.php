<?php
/**
 * Shared portfolio analytics endpoint.
 * GET  /portfolio/api/analytics.php -> current statistics
 * POST /portfolio/api/analytics.php -> records one page view
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

// Set this to the exact frontend origin when the API is hosted separately.
$allowedOrigin = getenv('ANALYTICS_ALLOWED_ORIGIN') ?: '*';
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    http_response_code(405);
    header('Allow: GET, POST, OPTIONS');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$file = __DIR__ . '/../storage/analytics.json';
$directory = dirname($file);
if (!is_dir($directory)) {
    mkdir($directory, 0750, true);
}

$handle = fopen($file, 'c+');
if ($handle === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Analytics storage is unavailable']);
    exit;
}

if (!flock($handle, LOCK_EX)) {
    fclose($handle);
    http_response_code(503);
    echo json_encode(['error' => 'Analytics storage is busy']);
    exit;
}

rewind($handle);
$contents = stream_get_contents($handle);
$data = json_decode($contents ?: '{}', true);
if (!is_array($data)) {
    $data = [];
}

$data['views'] = max(0, (int)($data['views'] ?? 0));
$data['unique_sessions'] = max(0, (int)($data['unique_sessions'] ?? 0));
$data['updated_at'] = gmdate('c');
$data['daily'] = is_array($data['daily'] ?? null) ? $data['daily'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $today = gmdate('Y-m-d');
    $data['views']++;
    $data['daily'][$today] = max(0, (int)($data['daily'][$today] ?? 0)) + 1;

    // A session ID lets the frontend avoid counting repeated refreshes as unique sessions.
    $payload = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (is_array($payload) && !empty($payload['session_id'])) {
        $data['unique_sessions'] = max(0, (int)($data['unique_sessions'] ?? 0));
    }
}

rewind($handle);
ftruncate($handle, 0);
fwrite($handle, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
fflush($handle);
flock($handle, LOCK_UN);
fclose($handle);

echo json_encode([
    'views' => $data['views'],
    'unique_sessions' => $data['unique_sessions'],
    'today' => $data['daily'][gmdate('Y-m-d')] ?? 0,
    'updated_at' => $data['updated_at']
], JSON_UNESCAPED_SLASHES);
