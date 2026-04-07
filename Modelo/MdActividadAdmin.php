<?php
// Modelo/MdActividadAdmin.php
require_once __DIR__ . '/../Config/config.php';

class MdActividadAdmin
{
    private const TABLE = 'actividades_proceso';

    public static function listarPorProceso(int $procesoId): array
    {
        $db = db();

        $sql = "
            SELECT
                a.id,
                a.proceso_id,
                a.tipo_id,
                a.fecha,
                a.comentario,
                a.created_at,
                a.updated_at,

                COALESCE(ta.nombre, '') AS tipo_nombre,
                COALESCE(ta.nombre, '') AS tipo_codigo,
                COALESCE(ta.nombre, '') AS titulo

            FROM " . self::TABLE . " a
            LEFT JOIN tipos_actividad ta
                ON ta.id = a.tipo_id
            WHERE a.proceso_id = :proceso_id
            ORDER BY a.fecha ASC, a.id ASC
        ";

        $st = $db->prepare($sql);
        $st->execute([
            ':proceso_id' => $procesoId
        ]);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtener(int $id): ?array
    {
        $db = db();

        $sql = "
            SELECT
                a.id,
                a.proceso_id,
                a.tipo_id,
                a.fecha,
                a.comentario,
                a.created_at,
                a.updated_at,

                COALESCE(ta.nombre, '') AS tipo_nombre,
                COALESCE(ta.nombre, '') AS tipo_codigo,
                COALESCE(ta.nombre, '') AS titulo

            FROM " . self::TABLE . " a
            LEFT JOIN tipos_actividad ta
                ON ta.id = a.tipo_id
            WHERE a.id = :id
            LIMIT 1
        ";

        $st = $db->prepare($sql);
        $st->execute([
            ':id' => $id
        ]);

        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function guardar(array $data): int
    {
        $db = db();

        $tipoId = (int)($data['tipo_actividad_id'] ?? $data['tipo_id'] ?? 0);
        $procesoId = (int)($data['proceso_id'] ?? 0);
        $fecha = trim((string)($data['fecha'] ?? ''));
        $comentario = trim((string)($data['comentario'] ?? ''));

        $sql = "
            INSERT INTO " . self::TABLE . " (
                proceso_id,
                tipo_id,
                fecha,
                comentario
            ) VALUES (
                :proceso_id,
                :tipo_id,
                :fecha,
                :comentario
            )
        ";

        $st = $db->prepare($sql);
        $st->execute([
            ':proceso_id' => $procesoId,
            ':tipo_id'    => $tipoId,
            ':fecha'      => $fecha,
            ':comentario' => $comentario !== '' ? $comentario : null,
        ]);

        return (int)$db->lastInsertId();
    }

    public static function eliminar(int $id): bool
    {
        $db = db();

        $sql = "DELETE FROM " . self::TABLE . " WHERE id = :id";
        $st = $db->prepare($sql);

        return $st->execute([
            ':id' => $id
        ]);
    }

    public static function listarTiposActividad(string $modulo = 'PAC'): array
    {
        $db = db();

        $sql = "
        SELECT id, nombre
        FROM tipos_actividad
        WHERE modulo = :modulo
        ORDER BY id ASC
    ";

        $st = $db->prepare($sql);
        $st->execute([
            ':modulo' => strtoupper(trim($modulo))
        ]);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
