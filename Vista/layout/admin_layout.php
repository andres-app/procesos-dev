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
$hrefReportes  = "/public/admin/reportes";
$hrefLogout    = "/public/admin/logout";
?>
<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= htmlspecialchars($titulo) ?></title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" />

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

        /* Desktop sticky */
        @media (min-width:1024px) {
            #adminSidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                align-self: flex-start;
            }
        }

        .header-title {
            min-width: 0;
        }

        /* ===== DataTables estilo suave ===== */
        .dataTables_wrapper {
            padding: 12px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 12px;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 10px;
            background: #fff;
            outline: none;
        }

        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 2px rgba(148, 163, 184, .15);
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 14px;
            font-size: 13px;
            color: #475569;
        }

        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 0 !important;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 0 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            min-width: 38px !important;
            height: 38px !important;
            padding: 0 12px !important;
            margin: 0 !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            color: #334155 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1 !important;
            box-shadow: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0f172a !important;
            color: #fff !important;
            border-color: #0f172a !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: .45;
            cursor: not-allowed !important;
            background: #fff !important;
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e2e8f0;
        }

        table.dataTable thead th {
            border-bottom: 1px solid #e2e8f0 !important;
        }

        table.dataTable tbody td {
            border-bottom: 1px solid #f1f5f9;
        }

        @media (max-width:640px) {

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none !important;
                text-align: center !important;
                justify-content: center;
            }

            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length {
                text-align: left !important;
            }
        }

        /* ===== TOASTER GLOBAL ===== */
        .toast-wrap {
            width: min(380px, calc(100vw - 2rem));
            pointer-events: none;
        }

        .toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border-radius: 18px;
            border: 1px solid rgba(226, 232, 240, .95);
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(10px);
            box-shadow: 0 16px 40px rgba(15, 23, 42, .12);
            padding: 14px 14px 12px;
            color: #0f172a;
            overflow: hidden;
            position: relative;
            transform: translateY(-8px);
            opacity: 0;
            animation: toastIn .22s ease forwards;
        }

        .toast--success {
            border-left: 4px solid #10b981;
        }

        .toast--error {
            border-left: 4px solid #ef4444;
        }

        .toast--warning {
            border-left: 4px solid #f59e0b;
        }

        .toast--info {
            border-left: 4px solid #3b82f6;
        }

        .toast__icon {
            flex: 0 0 auto;
            width: 22px;
            height: 22px;
            margin-top: 1px;
        }

        .toast--success .toast__icon {
            color: #10b981;
        }

        .toast--error .toast__icon {
            color: #ef4444;
        }

        .toast--warning .toast__icon {
            color: #f59e0b;
        }

        .toast--info .toast__icon {
            color: #3b82f6;
        }

        .toast__body {
            min-width: 0;
            flex: 1 1 auto;
        }

        .toast__title {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .toast__text {
            font-size: 13px;
            line-height: 1.4;
            color: #475569;
            word-break: break-word;
        }

        .toast__close {
            pointer-events: auto;
            flex: 0 0 auto;
            width: 28px;
            height: 28px;
            border-radius: 10px;
            border: 1px solid rgba(226, 232, 240, .95);
            background: #fff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .15s ease;
        }

        .toast__close:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .toast__progress {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            transform-origin: left center;
            animation: toastProgress linear forwards;
        }

        .toast--success .toast__progress {
            background: #10b981;
        }

        .toast--error .toast__progress {
            background: #ef4444;
        }

        .toast--warning .toast__progress {
            background: #f59e0b;
        }

        .toast--info .toast__progress {
            background: #3b82f6;
        }

        .toast--closing {
            animation: toastOut .18s ease forwards;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            to {
                opacity: 0;
                transform: translateY(-6px) scale(.98);
            }
        }

        @keyframes toastProgress {
            from {
                transform: scaleX(1);
            }

            to {
                transform: scaleX(0);
            }
        }
    </style>

</head>

<body class="bg-slate-50 text-slate-900">

    <div class="min-h-screen flex">

        <!-- overlay móvil -->

        <div id="overlay"
            class="fixed inset-0 bg-black/30 hidden z-40 lg:hidden"></div>

        <?php require __DIR__ . '/admin_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <?php require __DIR__ . '/admin_header.php'; ?>

            <main class="p-4 lg:p-5 min-w-0">