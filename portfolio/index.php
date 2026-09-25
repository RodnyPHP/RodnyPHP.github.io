<?php
require_once __DIR__ . '/includes/header.php';
$data = json_decode(@file_get_contents(__DIR__ . '/storage/data.json'), true) ?: [];
$profile = $data['profile'] ?? ['name' => 'RodnyPHP', 'bio' => 'Web developer'];
?><main class="container"><section class="hero"><h1><?= htmlspecialchars($profile['name']) ?></h1><p><?= htmlspecialchars($profile['bio']) ?></p></section><section><h2>Links</h2><ul><?php foreach (($data['links'] ?? []) as $link): ?><li><a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['title']) ?></a></li><?php endforeach; ?></ul></section></main><?php require_once __DIR__ . '/includes/footer.php'; ?>