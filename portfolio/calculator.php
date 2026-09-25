<?php
// PHP-compatible entry point. Interactive calculations are performed in calculator.js.
$initial = htmlspecialchars((string)($_GET['value'] ?? ''), ENT_QUOTES, 'UTF-8');
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Programmer Calculator | RodnyPHP</title>
  <link rel="stylesheet" href="assets/css/calculator.css">
</head>
<body>
  <main class="calculator" data-initial="<?= $initial ?>">
    <header><a href="index.html">← Portfolio</a><h1>Programmer Calculator</h1><p>Binary · octal · decimal · hexadecimal · bitwise operations</p></header>
    <section class="display-panel"><div id="expression" aria-live="polite"></div><output id="display" aria-label="Calculator result">0</output></section>
    <section class="base-row" aria-label="Number base"><button data-base="2">BIN</button><button data-base="8">OCT</button><button data-base="10" class="active">DEC</button><button data-base="16">HEX</button><span id="word-size">32-bit unsigned</span></section>
    <section class="keypad" aria-label="Calculator keypad">
      <button data-action="clear" class="danger">AC</button><button data-action="backspace">⌫</button><button data-value="&">AND</button><button data-value="|">OR</button><button data-value="^">XOR</button>
      <button data-value="~">NOT</button><button data-value="<<">SHL</button><button data-value=">>">SHR</button><button data-action="memory-clear">MC</button><button data-action="memory-recall">MR</button>
      <button data-value="7">7</button><button data-value="8">8</button><button data-value="9">9</button><button data-value="/">÷</button><button data-action="memory-add">M+</button>
      <button data-value="4">4</button><button data-value="5">5</button><button data-value="6">6</button><button data-value="*">×</button><button data-action="memory-subtract">M−</button>
      <button data-value="1">1</button><button data-value="2">2</button><button data-value="3">3</button><button data-value="-">−</button><button data-action="equals" class="equals">=</button>
      <button data-value="0" class="wide">0</button><button data-value=".">.</button><button data-value="+">+</button>
    </section>
    <section class="history"><h2>History</h2><ol id="history"></ol></section>
  </main>
  <script src="assets/js/calculator.js"></script>
</body>
</html>
