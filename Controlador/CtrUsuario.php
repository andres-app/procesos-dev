<?php
require_once __DIR__ . '/../Modelo/MdUsuariosAdmin.php';
require_once __DIR__ . '/AuthAdmin.php';
require_once __DIR__ . '/../Config/config.php';

class CtrUsuario
{
    public static function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si ya inició sesión en el PWA, no mostrar login otra vez
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $err = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = trim($_POST['user'] ?? '');
            $p = (string)($_POST['pass'] ?? '');

            if ($u === '' || $p === '') {
                $err = 'Complete usuario y contraseña.';
            } else {
                $user = MdUsuariosAdmin::obtenerPorUsuarioOCorreo($u);

                if (!$user) {
                    $err = 'No existe un usuario activo con ese usuario o correo.';
                } elseif (!password_verify($p, $user['password_hash'])) {
                    $err = 'La contraseña no coincide.';
                } else {
                    // Si tiene 2FA activo, enviar a verificación antes de entrar
                    if ((int)($user['twofa_enabled'] ?? 0) === 1 && !empty($user['twofa_secret'])) {
                        $_SESSION['user_2fa_pending'] = [
                            'id'       => (int)$user['id'],
                            'username' => $user['username'],
                            'rol'      => $user['rol'] ?? 'usuario',
                        ];

                        header('Location: ' . BASE_URL . '/verify-2fa');
                        exit;
                    }

                    // Sesión final PWA
                    $_SESSION['user_id']   = (int)$user['id'];
                    $_SESSION['user_name'] = $user['username'];
                    $_SESSION['user_rol']  = $user['rol'] ?? 'usuario';

                    header('Location: ' . BASE_URL . '/dashboard');
                    exit;
                }
            }
        }

        require __DIR__ . '/../Vista/modulos/login.php';
    }

    public static function verify2fa(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_2fa_pending']['id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $err = '';
        $userId = (int)$_SESSION['user_2fa_pending']['id'];
        $user   = MdUsuariosAdmin::obtenerPorId($userId);

        if (!$user || (int)($user['estado'] ?? 0) !== 1) {
            unset($_SESSION['user_2fa_pending']);
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');

            if (empty($user['twofa_secret']) || (int)($user['twofa_enabled'] ?? 0) !== 1) {
                $err = 'El doble factor no está configurado correctamente.';
            } elseif (!AuthAdmin::verifyTotp($user['twofa_secret'], $code, 1)) {
                $err = 'Código inválido o expirado.';
            } else {
                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_rol']  = $user['rol'] ?? 'usuario';

                unset($_SESSION['user_2fa_pending']);

                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            }
        }

        require __DIR__ . '/../Vista/modulos/verify_2fa.php';
    }

    public static function setup2fa(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $user   = MdUsuariosAdmin::obtenerPorId($userId);

        if (!$user) {
            session_destroy();
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $issuer = 'Seguimiento de Procesos';
        $err = '';
        $ok  = '';

        if (empty($_SESSION['user_2fa_setup_secret'])) {
            $_SESSION['user_2fa_setup_secret'] = !empty($user['twofa_secret'])
                ? $user['twofa_secret']
                : AuthAdmin::generateBase32Secret();
        }

        $secret      = $_SESSION['user_2fa_setup_secret'];
        $accountName = !empty($user['email'])
            ? 'PWA - ' . $user['email']
            : 'PWA - ' . $user['username'];

        $otpAuthUri = AuthAdmin::getOtpAuthUri($issuer, $accountName, $secret);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');

            if (!AuthAdmin::verifyTotp($secret, $code, 1)) {
                $err = 'El código no es válido. Revise la clave e intente otra vez.';
            } else {
                MdUsuariosAdmin::guardarTwofa($userId, $secret);
                unset($_SESSION['user_2fa_setup_secret']);
                $ok = 'Doble factor activado correctamente.';
            }
        }

        require __DIR__ . '/../Vista/modulos/setup_2fa.php';
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_rol']);
        unset($_SESSION['user_2fa_pending'], $_SESSION['user_2fa_setup_secret']);

        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public static function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}