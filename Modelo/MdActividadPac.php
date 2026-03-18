<?php
// Modelo/MdActividadPac.php
require_once __DIR__ . '/../Config/config.php';

class MdPacActividad
{
    public static function listarPorPac(int $pacId): array
    {
        $db = db();

        $sql = "
            SELECT
                id,
                pac_id,
                titulo,
                fecha,
                comentario,
                created_at,
                updated_at
            FROM actividades_pac
            WHERE pac_id = :pac_id
            ORDER BY fecha ASC, id ASC
        ";

        $st = $db->prepare($sql);
        $st->execute([':pac_id' => $pacId]);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function guardar(array $data): bool
    {
        $db = db();

        $sql = "
            INSERT INTO actividades_pac (
                pac_id,
                titulo,
                fecha,
                comentario
            ) VALUES (
                :pac_id,
                :titulo,
                :fecha,
                :comentario
            )
        ";

        $st = $db->prepare($sql);

        return $st->execute([
            ':pac_id'     => (int)$data['pac_id'],
            ':titulo'     => trim((string)($data['titulo'] ?? '')),
            ':fecha'      => trim((string)($data['fecha'] ?? '')),
            ':comentario' => trim((string)($data['comentario'] ?? '')) ?: null,
        ]);
    }
}