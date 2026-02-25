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

  case 'indicadores':
    require '../Vista/modulos/indicadores.php';
    break;

  case 'alertas':
    require '../Vista/modulos/alertas.php';
    break;

  case 'reportes':
    $sub = $ruta[1] ?? 'index'; // reportes/derivados

    $baseVista = __DIR__ . '/../Vista/modulos'; // <-- base real

    switch ($sub) {
      case 'derivados':
        $file = $baseVista . '/reportes/derivados.php';
        break;

      case 'procesos':
        $file = $baseVista . '/reportes/procesos.php';
        break;

      case 'consolidado':
        $file = $baseVista . '/reportes/consolidado.php';
        break;

      default:
        $file = $baseVista . '/reportes.php'; // tu pantalla con los 4 cards
        break;
    }

    if (!is_file($file)) {
      http_response_code(404);
      echo "<h1 style='color:white'>404 - No existe: {$file}</h1>";
      exit;
    }

    require $file;
    break;

  case 'procesos':
    require '../Vista/modulos/procesos.php';
    break;

  case 'pac':
    require '../Vista/modulos/pac.php';
    break;

  case 'presupuesto':
    require '../Vista/modulos/presupuesto.php';
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
