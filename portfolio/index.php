<?php
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="RodnyPHP — web developer portfolio">
  <title>RodnyPHP | Portfolio</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header"><div class="container header-inner">
  <a class="brand" href="index.php">RodnyPHP</a>
  <nav class="main-nav"><a href="#about">About</a><a href="#projects">Projects</a><a href="#links">Links</a><a href="admin/index.php">Admin</a></nav>
</div></header>
<section class="banner-shell"><div class="container banner-spotlight">
  <div class="banner-copy"><span class="eyebrow">Featured</span><h1 id="banner-title">Building useful things for the web.</h1><p id="banner-subtitle">Responsive interfaces, developer tools, and clean digital experiences.</p><div class="banner-actions"><a class="button primary" href="admin/index.php">Manage Banner</a><a class="button ghost" href="#projects">View Projects</a></div></div>
  <div class="banner-visual"><div class="banner-card"><span>Live Banner</span><strong id="banner-card-title">Portfolio Hub</strong><small id="banner-card-subtitle">Loading...</small></div></div>
</div></section>
<main class="container">
<section id="about" class="section-block"><div class="section-heading"><p class="eyebrow">About</p><h2>Web developer and PHP enthusiast</h2></div><p id="bio" class="lead">Building responsive, accessible web experiences.</p></section>
<section id="projects" class="section-block"><div class="section-heading"><p class="eyebrow">Projects</p><h2>Recent work</h2></div><div class="cards horizontal-cards"><article class="card"><h3>Programmer Calculator</h3><p>Binary, hexadecimal, octal, and decimal tools.</p><a href="calculator.php">View demo</a></article><article class="card"><h3>Admin Dashboard</h3><p>PHP banner management with persistent storage.</p><a href="admin/index.php">Open admin</a></article></div></section>
<section id="links" class="section-block"><div class="section-heading"><p class="eyebrow">Links</p><h2>Find me online</h2></div><div id="link-list" class="cards"></div></section>
</main>
<footer class="site-footer"><div class="container footer-inner"><span>© 2026 RodnyPHP</span><span id="analytics-status">Analytics: loading...</span></div></footer>
<script src="assets/js/app.js"></script>
</body></html>