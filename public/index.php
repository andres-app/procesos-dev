<?php
session_start();

/*
|--------------------------------------------------------------------------
| BASE URL DINÁMICA (LOCAL + PRODUCCIÓN)
|--------------------------------------------------------------------------
| Detecta automáticamente si el proyecto está en subcarpeta o en dominio
*/
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

define('BASE_URL', $basePath === '' ? '' : $basePath);

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
$url = $_GET['url'] ?? 'login';
$ruta = explode('/', trim($url, '/'));
$modulo = $ruta[0];

/*
|--------------------------------------------------------------------------
| RUTAS
|--------------------------------------------------------------------------
*/
switch ($modulo) {

  case 'login':
    require '../Controlador/CtrUsuario.php';
    CtrUsuario::login();
    break;

  case 'gastos':
    require '../Controlador/CtrGasto.php';
    CtrGasto::inicio();
    break;

  case 'dashboard':
    require '../Vista/modulos/dashboard.php';
    break;

  case 'perfil':
    require '../Vista/modulos/perfil.php';
    break;

  case 'ingresos':
    require '../Vista/modulos/ingresos.php';
    break;

  case 'reportes':
    require '../Vista/modulos/reportes.php';
    break;

  case 'ahorro':
    require '../Vista/modulos/ahorro.php';
    break;

  case 'categorias':
    require '../Vista/modulos/categorias.php';
    break;

  case 'metas':
    require '../Vista/modulos/metas.php';
    break;

  case 'procesos':
    require '../Vista/modulos/procesos.php';
    break;

  case 'logout':
    session_destroy();
    header("Location: login");
    exit;



  default:
    http_response_code(404);
    echo '<h1 style="color:white">404 - Página no encontrada</h1>';
    break;
}
