<?php
header('Content-Type: application/json');
$dataFile = __DIR__ . '/../storage/data.json';
$data = json_decode(@file_get_contents($dataFile), true) ?: [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') { echo json_encode($data['links'] ?? []); exit; }
http_response_code(405); echo json_encode(['error' => 'Method not allowed']);