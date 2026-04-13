<?php
require_once __DIR__ . '/../Modelo/MdUsuariosAdmin.php';
require_once __DIR__ . '/AuthAdmin.php';

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
                    if ((int)($admin['twofa_enabled'] ?? 0) === 1 && !empty($admin['twofa_secret'])) {
                        $_SESSION['admin_2fa_pending'] = [
                            'id'       => (int)$admin['id'],
                            'username' => $admin['username'],
                            'rol'      => $admin['rol'],
                        ];

                        header('Location: /public/admin/verify-2fa');
                        exit;
                    }

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

    public static function verify2fa(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['admin_2fa_pending']['id'])) {
            header('Location: /public/admin/login');
            exit;
        }

        $err = '';
        $userId = (int)$_SESSION['admin_2fa_pending']['id'];
        $user = MdUsuariosAdmin::obtenerPorId($userId);

        if (!$user || (int)$user['estado'] !== 1) {
            unset($_SESSION['admin_2fa_pending']);
            header('Location: /public/admin/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');

            if (empty($user['twofa_secret']) || (int)($user['twofa_enabled'] ?? 0) !== 1) {
                $err = 'El doble factor no está configurado correctamente.';
            } elseif (!AuthAdmin::verifyTotp($user['twofa_secret'], $code, 1)) {
                $err = 'Código inválido o expirado.';
            } else {
                $_SESSION['admin_user_id'] = (int)$user['id'];
                $_SESSION['admin_user']    = $user['username'];
                $_SESSION['admin_rol']     = $user['rol'];

                unset($_SESSION['admin_2fa_pending']);

                header('Location: /public/admin/dashboard');
                exit;
            }
        }

        require __DIR__ . '/../Vista/modulos/admin/verify_2fa.php';
    }

    public static function setup2fa(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['admin_user_id'])) {
            header('Location: /public/admin/login');
            exit;
        }

        $userId = (int)$_SESSION['admin_user_id'];
        $user   = MdUsuariosAdmin::obtenerPorId($userId);

        if (!$user) {
            session_destroy();
            header('Location: /public/admin/login');
            exit;
        }

        $issuer = 'PROCESOS-DEV';
        $err = '';
        $ok  = '';

        if (empty($_SESSION['admin_2fa_setup_secret'])) {
            $_SESSION['admin_2fa_setup_secret'] = !empty($user['twofa_secret'])
                ? $user['twofa_secret']
                : AuthAdmin::generateBase32Secret();
        }

        $secret = $_SESSION['admin_2fa_setup_secret'];
        $accountName = !empty($user['email']) ? $user['email'] : $user['username'];
        $otpAuthUri = AuthAdmin::getOtpAuthUri($issuer, $accountName, $secret);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');

            if (!AuthAdmin::verifyTotp($secret, $code, 1)) {
                $err = 'El código no es válido. Revise la clave e intente otra vez.';
            } else {
                MdUsuariosAdmin::guardarTwofa($userId, $secret);
                unset($_SESSION['admin_2fa_setup_secret']);
                $ok = 'Doble factor activado correctamente.';
            }
        }

        require __DIR__ . '/../Vista/modulos/admin/setup_2fa.php';
    }
}