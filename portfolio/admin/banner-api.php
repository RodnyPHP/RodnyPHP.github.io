<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function ensureSqlite(): array {
    $dbFile = __DIR__ . '/banner.db';
    $db = new SQLite3($dbFile, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    $db->exec("
        CREATE TABLE IF NOT EXISTS banners (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            subtitle TEXT,
            link TEXT,
            image TEXT,
            active INTEGER NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    $count = $db->querySingle('SELECT COUNT(*) FROM banners');
    if ((int)$count === 0) {
        $db->exec("INSERT INTO banners (title, subtitle, link, image, active) VALUES ('Portfolio Hub', 'Responsive interfaces, developer tools, and marketing-ready content blocks.', '../index.html', '', 1)");
    }

    return [$db, $dbFile];
}

function readBanners(): array {
    try {
        [$db] = ensureSqlite();
        $result = $db->query('SELECT * FROM banners ORDER BY active DESC, id DESC');
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'subtitle' => $row['subtitle'],
                'link' => $row['link'],
                'image' => $row['image'],
                'active' => (bool)$row['active'],
            ];
        }
        $db->close();
        return $rows;
    } catch (Throwable $e) {
        $jsonFile = __DIR__ . '/banners.json';
        if (!file_exists($jsonFile)) {
            file_put_contents($jsonFile, json_encode([
                [
                    'id' => 1,
                    'title' => 'Portfolio Hub',
                    'subtitle' => 'Responsive interfaces, developer tools, and marketing-ready content blocks.',
                    'link' => '../index.html',
                    'image' => '',
                    'active' => true,
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        $data = json_decode(file_get_contents($jsonFile), true);
        return is_array($data) ? $data : [];
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode(readBanners());
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        $title = trim((string)($input['title'] ?? ''));
        if ($title === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            exit;
        }

        try {
            [$db] = ensureSqlite();
            if (!empty($input['active'])) {
                $db->exec('UPDATE banners SET active = 0');
            }
            $stmt = $db->prepare('INSERT INTO banners (title, subtitle, link, image, active) VALUES (:title, :subtitle, :link, :image, :active)');
            $stmt->bindValue(':title', $title, SQLITE3_TEXT);
            $stmt->bindValue(':subtitle', (string)($input['subtitle'] ?? ''), SQLITE3_TEXT);
            $stmt->bindValue(':link', (string)($input['link'] ?? ''), SQLITE3_TEXT);
            $stmt->bindValue(':image', (string)($input['image'] ?? ''), SQLITE3_TEXT);
            $stmt->bindValue(':active', !empty($input['active']) ? 1 : 0, SQLITE3_INTEGER);
            $stmt->execute();
            $db->close();
            echo json_encode(['success' => true, 'message' => 'Banner saved']);
        } catch (Throwable $e) {
            $jsonFile = __DIR__ . '/banners.json';
            $rows = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
            if (!is_array($rows)) $rows = [];
            $rows[] = [
                'id' => time(),
                'title' => $title,
                'subtitle' => (string)($input['subtitle'] ?? ''),
                'link' => (string)($input['link'] ?? ''),
                'image' => (string)($input['image'] ?? ''),
                'active' => !empty($input['active']),
            ];
            file_put_contents($jsonFile, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            echo json_encode(['success' => true, 'message' => 'Banner saved (fallback)']);
        }
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid id']);
            exit;
        }

        try {
            [$db] = ensureSqlite();
            $db->exec('DELETE FROM banners WHERE id = ' . $id);
            $db->close();
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            $jsonFile = __DIR__ . '/banners.json';
            if (file_exists($jsonFile)) {
                $rows = json_decode(file_get_contents($jsonFile), true);
                if (is_array($rows)) {
                    $rows = array_values(array_filter($rows, fn($row) => (int)($row['id'] ?? 0) !== $id));
                    file_put_contents($jsonFile, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
            }
            echo json_encode(['success' => true]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
