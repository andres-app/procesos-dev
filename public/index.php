<?php
session_start();

/*
|--------------------------------------------------------------------------
| BASE URL DINÁMICA (LOCAL + PRODUCCIÓN)
|--------------------------------------------------------------------------
*/
define('BASE_URL', '/public');
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
    $sub = $ruta[1] ?? ''; // admin/login, admin/dashboard, admin/pac...

    // Si entran a /admin -> redirige
    if ($sub === '') {
      if (!empty($_SESSION['admin_user'])) {
        header("Location: " . rtrim(BASE_URL, '/') . "/admin/dashboard");
      } else {
        header("Location: " . BASE_URL . "/admin/login");
      }
      exit;
    }

    // Rutas públicas admin
    if ($sub === 'login') {
      require __DIR__ . '/../Vista/modulos/admin/login.php';
      exit;
    }

    if ($sub === 'logout') {
      require __DIR__ . '/../Vista/modulos/admin/logout.php';
      exit;
    }

    // Protegidas admin
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

      default:
        http_response_code(404);
        echo '<h1 style="color:white">404 - Admin: Página no encontrada</h1>';
        break;
    }
    break;

  /* =========================
     PUBLICO (tu router actual)
     ========================= */

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