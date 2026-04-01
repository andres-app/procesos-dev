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
                a.titulo,
                a.fecha,
                a.comentario,
                a.created_at,
                a.updated_at,

                COALESCE(ta.nombre, '') AS tipo_nombre,
                COALESCE(ta.nombre, '') AS tipo_codigo

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

    public static function guardar(array $data): int
    {
        $db = db();

        $sql = "
            INSERT INTO " . self::TABLE . " (
                proceso_id,
                tipo_id,
                titulo,
                fecha,
                comentario
            ) VALUES (
                :proceso_id,
                :tipo_id,
                :titulo,
                :fecha,
                :comentario
            )
        ";

        $st = $db->prepare($sql);
        $st->execute([
            ':proceso_id' => (int)$data['proceso_id'],
            ':tipo_id'    => (int)$data['tipo_id'],
            ':titulo'     => trim((string)$data['titulo']),
            ':fecha'      => $data['fecha'],
            ':comentario' => $data['comentario'] ?? null,
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

    public static function listarTiposActividad(): array
    {
        $db = db();

        $sql = "
            SELECT id, nombre
            FROM tipos_actividad
            ORDER BY id ASC
        ";

        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}