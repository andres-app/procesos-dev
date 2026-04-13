<?php
require_once __DIR__ . '/../Config/config.php';

class MdUsuariosAdmin
{
    public static function obtenerPorUsuarioOCorreo(string $user): ?array
    {
        $db = db();

        $sql = "SELECT id, username, email, password_hash, rol, estado
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
}