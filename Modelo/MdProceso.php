<?php
require_once __DIR__ . '/../Config/config.php';

class MdProceso
{
  public static function listar(array $filtros = []): array
  {
    $db = db();

    $sql = "
      SELECT
        p.id,
        p.codigo_proceso AS proceso,
        p.expediente,
        p.obac,
        p.descripcion,
        COALESCE(ep.codigo,'') AS estado,
        p.estimado,
        p.anio_convocatoria,
        p.periodo
      FROM procesos p
      LEFT JOIN estados_proceso ep ON ep.id = p.estado_id
      WHERE 1=1
    ";

    $params = [];

    if (!empty($filtros['periodo'])) {
      $sql .= " AND p.periodo = :periodo";
      $params[':periodo'] = (int)$filtros['periodo'];
    }

    if (!empty($filtros['anio_convocatoria'])) {
      $sql .= " AND p.anio_convocatoria = :anio_conv";
      $params[':anio_conv'] = (int)$filtros['anio_convocatoria'];
    }

    if (!empty($filtros['estado'])) {
      $sql .= " AND ep.codigo = :estado";
      $params[':estado'] = strtoupper(trim((string)$filtros['estado']));
    }

    if (!empty($filtros['q'])) {
      $q = trim((string)$filtros['q']);
      $sql .= " AND (
        p.codigo_proceso LIKE :q OR
        p.expediente LIKE :q OR
        p.obac LIKE :q OR
        p.descripcion LIKE :q
      )";
      $params[':q'] = "%{$q}%";
    }

    $sql .= " ORDER BY p.id DESC";

    $st = $db->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
  }
}