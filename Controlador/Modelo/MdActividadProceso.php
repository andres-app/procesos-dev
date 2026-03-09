<?php
require_once __DIR__ . '/../Config/config.php';

class MdActividadProceso
{
    public static function listarPorProceso(int $procesoId): array
    {
        $db = db();

        $sql = "
    SELECT
      ap.id,
      ap.proceso_id,
      ap.tipo_id,
      ap.titulo,
      ap.fecha,
      ap.comentario,

      ta.codigo  AS tipo_codigo,
      ta.nombre  AS tipo_nombre,
      ta.orden_base AS tipo_orden_base
    FROM actividades_proceso ap
    LEFT JOIN tipos_actividad ta ON ta.id = ap.tipo_id
    WHERE ap.proceso_id = :pid
    ORDER BY ap.fecha ASC, ap.id ASC
  ";

        $st = $db->prepare($sql);
        $st->execute([':pid' => $procesoId]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
