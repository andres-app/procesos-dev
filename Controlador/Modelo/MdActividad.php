<?php
declare(strict_types=1);

require_once __DIR__ . '/../Config/config.php';

final class MdActividad
{
  public static function listarPorProceso(int $idProceso): array
  {
    $db = db();

    $sql = "
      SELECT
        ap.id,
        ap.titulo,
        ap.comentario,
        ap.fecha,
        ta.codigo AS tipo_codigo,
        ta.nombre AS tipo_nombre
      FROM actividades_proceso ap
      LEFT JOIN tipos_actividad ta ON ta.id = ap.tipo_id
      WHERE ap.proceso_id = :id
      ORDER BY ap.fecha ASC, ap.id ASC
    ";

    $st = $db->prepare($sql);
    $st->execute([':id' => $idProceso]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }
}