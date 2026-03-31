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
                ap.id,
                ap.pac_id,
                ap.tipo_actividad_id,
                ap.fecha,
                ap.comentario,
                ap.created_at,
                ap.updated_at,
                ta.nombre AS tipo_actividad_nombre,
                ta.estado AS tipo_actividad_estado
            FROM actividades_pac ap
            LEFT JOIN tipos_actividad ta
                ON ta.id = ap.tipo_actividad_id
            WHERE ap.pac_id = :pac_id
            ORDER BY ap.fecha ASC, ap.id ASC
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
                tipo_actividad_id,
                fecha,
                comentario
            ) VALUES (
                :pac_id,
                :tipo_actividad_id,
                :fecha,
                :comentario
            )
        ";

        $st = $db->prepare($sql);

        return $st->execute([
            ':pac_id'            => (int)($data['pac_id'] ?? 0),
            ':tipo_actividad_id' => (int)($data['tipo_actividad_id'] ?? 0),
            ':fecha'             => trim((string)($data['fecha'] ?? '')),
            ':comentario'        => trim((string)($data['comentario'] ?? '')) ?: null,
        ]);
    }

    public static function listarTiposActividad(): array
    {
        $db = db();

        $sql = "SELECT id, estado, nombre FROM tipos_actividad ORDER BY id ASC";
        $st = $db->query($sql);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}