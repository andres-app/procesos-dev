<?php
session_start();

/*
|--------------------------------------------------------------------------
| CONFIG (BASE_URL dinámico LOCAL + PRODUCCIÓN)
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../Config/config.php';

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
$url = $_GET['url'] ?? 'login';
$ruta = explode('/', trim($url, '/'));
$modulo = $ruta[0] ?? 'login';

/*
|--------------------------------------------------------------------------
| HELPERS ADMIN
|--------------------------------------------------------------------------
*/
function admin_require_login()
{
  if (empty($_SESSION['admin_user'])) {
    header("Location: " . BASE_URL . "/admin/login");
    exit;
  }
}

/*
|--------------------------------------------------------------------------
| RUTAS
|--------------------------------------------------------------------------
*/
switch ($modulo) {

  /* =========================
     ADMIN ( /admin/... )
     ========================= */
  case 'admin':
    $sub = $ruta[1] ?? '';

    if ($sub === '') {
      if (!empty($_SESSION['admin_user'])) {
        header("Location: " . BASE_URL . "/admin/dashboard");
      } else {
        header("Location: " . BASE_URL . "/admin/login");
      }
      exit;
    }

    if ($sub === 'login') {
      require __DIR__ . '/../Vista/modulos/admin/login.php';
      exit;
    }

    if ($sub === 'logout') {
      require __DIR__ . '/../Vista/modulos/admin/logout.php';
      exit;
    }

    admin_require_login();

    switch ($sub) {
      case 'dashboard':
        require __DIR__ . '/../Vista/modulos/admin/dashboard.php';
        break;

      case 'pac':
        require __DIR__ . '/../Vista/modulos/admin/pac.php';
        break;

      case 'procesos':
        require __DIR__ . '/../Vista/modulos/admin/procesos.php';
        break;

      case 'presupuesto':
        require __DIR__ . '/../Vista/modulos/admin/presupuesto.php';
        break;

      default:
        http_response_code(404);
        echo '<h1 style="color:white">404 - Admin: Página no encontrada</h1>';
        break;
    }
    break;

  /* =========================
     PÚBLICO
     ========================= */

  case 'login':
    require __DIR__ . '/../Controlador/CtrUsuario.php';
    CtrUsuario::login();
    break;

  case 'dashboard':
    require __DIR__ . '/../Vista/modulos/dashboard.php';
    break;

  case 'perfil':
    require __DIR__ . '/../Vista/modulos/perfil.php';
    break;

  case 'indicadores':
    require __DIR__ . '/../Vista/modulos/indicadores.php';
    break;

  case 'alertas':
    require __DIR__ . '/../Vista/modulos/alertas.php';
    break;

  case 'presupuesto':
    require __DIR__ . '/../Vista/modulos/presupuesto.php';
    break;

  case 'reportes':
    $sub = $ruta[1] ?? 'index';
    $baseVista = __DIR__ . '/../Vista/modulos';

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
        $file = $baseVista . '/reportes.php';
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
    require __DIR__ . '/../Vista/modulos/procesos.php';
    break;

  case 'pac':
    require __DIR__ . '/../Vista/modulos/pac.php';
    break;

  case 'presupuesto':
    require __DIR__ . '/../Vista/modulos/presupuesto.php';
    break;

  case 'logout':
    session_destroy();
    header("Location: " . BASE_URL . "/login");
    exit;

  default:
    http_response_code(404);
    echo '<h1 style="color:white">404 - Página no encontrada</h1>';
    break;
}
