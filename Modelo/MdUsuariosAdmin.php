<?php
require_once __DIR__ . '/../Config/config.php';

class MdUsuariosAdmin
{
    public static function obtenerPorUsuarioOCorreo(string $user): ?array
    {
        $db = db();

        $sql = "SELECT id, username, email, password_hash, rol, estado, twofa_enabled, twofa_secret
                FROM usuarios
                WHERE (username = :username OR email = :email)
                  AND estado = 1
                LIMIT 1";

        $st = $db->prepare($sql);
        $st->execute([
            ':username' => trim($user),
            ':email'    => trim($user),
        ]);

        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function obtenerPorId(int $id): ?array
    {
        $db = db();

        $sql = "SELECT id, username, email, password_hash, rol, estado, twofa_enabled, twofa_secret
                FROM usuarios
                WHERE id = :id
                LIMIT 1";

        $st = $db->prepare($sql);
        $st->execute([
            ':id' => $id
        ]);

        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function guardarTwofa(int $id, string $secret): bool
    {
        $db = db();

        $sql = "UPDATE usuarios
                SET twofa_secret = :secret,
                    twofa_enabled = 1
                WHERE id = :id";

        $st = $db->prepare($sql);

        return $st->execute([
            ':secret' => $secret,
            ':id'     => $id
        ]);
    }
}