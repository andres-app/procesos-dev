<?php
if (!defined('BASE_URL')) {
  $host = $_SERVER['HTTP_HOST'] ?? '';
  $isLocal = ($host === 'localhost' || $host === '127.0.0.1');
  define('BASE_URL', $isLocal ? '/procesos-dev/public' : '/public');
}