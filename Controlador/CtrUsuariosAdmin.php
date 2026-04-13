<?php
require_once __DIR__ . '/../Modelo/MdUsuariosAdmin.php';

class CtrUsuariosAdmin
{
    public static function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $err = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = trim($_POST['user'] ?? '');
            $p = (string)($_POST['pass'] ?? '');

            if ($u === '' || $p === '') {
                $err = 'Complete usuario y contraseña.';
            } else {
                $admin = MdUsuariosAdmin::obtenerPorUsuarioOCorreo($u);

                if (!$admin) {
                    $err = 'No existe un usuario activo con ese usuario o correo.';
                } elseif (!password_verify($p, $admin['password_hash'])) {
                    $err = 'La contraseña no coincide.';
                } else {
                    $_SESSION['admin_user_id'] = (int)$admin['id'];
                    $_SESSION['admin_user']    = $admin['username'];
                    $_SESSION['admin_rol']     = $admin['rol'];

                    header('Location: /public/admin/dashboard');
                    exit;
                }
            }
        }

        require __DIR__ . '/../Vista/modulos/admin/login.php';
    }
}