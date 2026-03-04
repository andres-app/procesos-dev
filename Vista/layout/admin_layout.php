<?php
$titulo = $titulo ?? 'Admin';
$active = $active ?? '';
$adminUser = $_SESSION['admin_user'] ?? 'Admin';

require_once __DIR__ . '/../../Config/config.php';

function acls($isActive)
{
    return $isActive
        ? 'bg-slate-900 text-white'
        : 'text-slate-700 hover:bg-slate-100';
}

$hrefDashboard = "/public/admin/dashboard";
$hrefPac       = "/public/admin/pac";
$hrefProcesos  = "/public/admin/procesos";
$hrefPresupuesto  = "/public/admin/presupuesto";
$hrefLogout    = "/public/admin/logout";
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titulo) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* ===== Compact default ===== */
        body {
            font-size: 14px;
        }

        /* ===== Sidebar width ===== */
        #adminSidebar {
            width: 240px;
            transition: all .2s ease;
        }

        body.sb-rail #adminSidebar {
            width: 72px;
        }

        body.sb-rail .sb-label,
        body.sb-rail .sb-brand-sub,
        body.sb-rail .sb-footer {
            display: none;
        }

        body.sb-rail .sb-ico {
            width: 100%;
            text-align: center;
        }

        /* Mobile drawer */
        @media (max-width:1023px) {
            #adminSidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                transform: translateX(-100%);
                z-index: 50;
                background: #fff;
            }

            body.sb-open #adminSidebar {
                transform: translateX(0);
            }
        }

        /* Desktop: sticky real */
        @media (min-width:1024px) {
            #adminSidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                align-self: flex-start;
                /* evita comportamientos raros dentro de flex */
            }
        }

        .header-title {
            min-width: 0;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex">

        <!-- Overlay móvil -->
        <div id="overlay" class="fixed inset-0 bg-black/30 hidden z-40 lg:hidden"></div>

        <?php require __DIR__ . '/admin_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <?php require __DIR__ . '/admin_header.php'; ?>

            <main class="p-4 lg:p-5 min-w-0">