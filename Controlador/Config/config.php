<?php
if (!defined('BASE_URL')) {
  $host = $_SERVER['HTTP_HOST'] ?? '';
  $isLocal = ($host === 'localhost' || $host === '127.0.0.1');
  define('BASE_URL', $isLocal ? '/procesos-dev/public' : '/public');
}

/* =========================
   CONEXIÓN PDO
========================= */
if (!function_exists('db')) {
  function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = '127.0.0.1';
    $dbname = 'u274409976_procesos';
    $user = 'u274409976_procesos';
    $pass = 'Procesos2804751$$$';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
  }
}