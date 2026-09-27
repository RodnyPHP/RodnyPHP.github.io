<?php
// Banner API - RESTful endpoint for banner management
// GET /admin/banner-api.php - fetch all banners
// POST /admin/banner-api.php - create banner
// DELETE /admin/banner-api.php?id=N - delete banner

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function initDb() {
    $file = __DIR__ . '/banner.db';
    $new = !file_exists($file);
    try {
        $db = new SQLite3($file);
        $db->exec("CREATE TABLE IF NOT EXISTS banners (id INTEGER PRIMARY KEY, title TEXT, subtitle TEXT, link TEXT, image TEXT, active INTEGER DEFAULT 0, created_at TEXT)");
        if ($new) {
            $db->exec("INSERT INTO banners VALUES (1, 'Portfolio Hub', 'Responsive interfaces and developer tools.', '../index.php', '', 1, datetime('now'))");
        }
        return $db;
    } catch (Exception $e) {
        return null;
    }
}

function getJson() {
    $file = __DIR__ . '/banners.json';
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([['id' => 1, 'title' => 'Portfolio Hub', 'subtitle' => 'Responsive interfaces and developer tools.', 'link' => '../index.php', 'image' => '', 'active' => 1, 'created_at' => date('c')]], JSON_PRETTY_PRINT));
    }
    return json_decode(file_get_contents($file), true) ?: [];
}

function putJson($data) {
    file_put_contents(__DIR__ . '/banners.json', json_encode($data, JSON_PRETTY_PRINT));
}

function read() {
    $db = initDb();
    if ($db) {
        $result = $db->query('SELECT * FROM banners ORDER BY active DESC, id DESC');
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = ['id' => (int)$row['id'], 'title' => $row['title'], 'subtitle' => $row['subtitle'], 'link' => $row['link'], 'image' => $row['image'], 'active' => (int)$row['active'], 'created_at' => $row['created_at']];
        }
        $db->close();
        return $rows;
    }
    return getJson();
}

function create($input) {
    $title = trim($input['title'] ?? '');
    if (!$title) {
        http_response_code(400);
        echo json_encode(['error' => 'Title required']);
        exit;
    }
    
    $data = ['title' => $title, 'subtitle' => trim($input['subtitle'] ?? ''), 'link' => trim($input['link'] ?? '../index.php'), 'image' => trim($input['image'] ?? ''), 'active' => (int)($input['active'] ?? 0)];
    
    $db = initDb();
    if ($db) {
        if ($data['active']) {
            $db->exec('UPDATE banners SET active = 0');
        }
        $stmt = $db->prepare('INSERT INTO banners (title, subtitle, link, image, active, created_at) VALUES (:title, :subtitle, :link, :image, :active, datetime("now"))');
        $stmt->bindValue(':title', $data['title'], SQLITE3_TEXT);
        $stmt->bindValue(':subtitle', $data['subtitle'], SQLITE3_TEXT);
        $stmt->bindValue(':link', $data['link'], SQLITE3_TEXT);
        $stmt->bindValue(':image', $data['image'], SQLITE3_TEXT);
        $stmt->bindValue(':active', $data['active'], SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        echo json_encode(['success' => true, 'message' => 'Created']);
        return;
    }
    
    $rows = getJson();
    if ($data['active']) {
        foreach ($rows as &$r) $r['active'] = 0;
    }
    $rows[] = array_merge(['id' => max(array_map(fn($r) => $r['id'], $rows)) + 1, 'created_at' => date('c')], $data);
    putJson($rows);
    echo json_encode(['success' => true, 'message' => 'Created']);
}

function delete($id) {
    $id = (int)$id;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ID']);
        exit;
    }
    
    $db = initDb();
    if ($db) {
        $db->exec("DELETE FROM banners WHERE id = $id");
        $db->close();
        echo json_encode(['success' => true]);
        return;
    }
    
    $rows = getJson();
    $rows = array_filter($rows, fn($r) => $r['id'] !== $id);
    putJson(array_values($rows));
    echo json_encode(['success' => true]);
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode(read());
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        create($input);
        break;
    case 'DELETE':
        $id = $_GET['id'] ?? 0;
        delete($id);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
