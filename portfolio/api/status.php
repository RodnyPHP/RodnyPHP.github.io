<?php
header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'message' => 'PHP backend active', 'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost', 'php_version' => phpversion(), 'sqlite3' => extension_loaded('sqlite3')]);
