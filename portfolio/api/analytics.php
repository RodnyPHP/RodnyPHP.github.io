<?php
// Analytics API - Track page views
// GET /api/analytics.php - get stats
// POST /api/analytics.php - record view

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$dir = __DIR__ . '/../storage';
if (!is_dir($dir)) mkdir($dir, 0755, true);

$file = $dir . '/analytics.json';
$handle = fopen($file, 'c+');

if (!$handle || !flock($handle, LOCK_EX)) {
    http_response_code(500);
    echo json_encode(['error' => 'Storage error']);
    exit;
}

rewind($handle);
$data = json_decode(stream_get_contents($handle) ?: '{}', true);
if (!is_array($data)) $data = [];

$data['views'] = (int)($data['views'] ?? 0);
$data['today'] = (int)($data['today'] ?? 0);
$data['unique_sessions'] = (int)($data['unique_sessions'] ?? 0);
$data['sessions'] = $data['sessions'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_decode(file_get_contents('php://input') ?: '{}', true);
    $sid = $payload['session_id'] ?? '';
    
    $data['views']++;
    $data['today']++;
    
    if ($sid && !in_array($sid, $data['sessions'])) {
        $data['sessions'][] = $sid;
        $data['unique_sessions'] = count($data['sessions']);
    }
}

rewind($handle);
ftruncate($handle, 0);
fwrite($handle, json_encode($data));
fflush($handle);
flock($handle, LOCK_UN);
fclose($handle);

echo json_encode(['views' => $data['views'], 'today' => $data['today'], 'unique_sessions' => $data['unique_sessions'], 'updated_at' => date('c')]);
