<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals('change-me', (string)($_POST['password'] ?? ''))) { $_SESSION['admin'] = true; header('Location: admin/'); exit; }
require_once __DIR__ . '/includes/header.php';
?><main class="container"><h1>Admin login</h1><form method="post"><label>Password <input type="password" name="password" required></label><button type="submit">Log in</button></form></main><?php require_once __DIR__ . '/includes/footer.php'; ?>