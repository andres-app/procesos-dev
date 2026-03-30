<?php
// Modelo/MdDashboardAdmin.php
require_once __DIR__ . '/../Config/config.php';

class MdDashboardAdmin
{
    public static function obtenerKpisGenerales(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                COUNT(*) AS total_pac,
                COALESCE(SUM(p.estimado), 0) AS total_estimado,
                COALESCE(SUM(p.certificado), 0) AS total_certificado,
                SUM(
                    CASE
                        WHEN p.inversiones IS NOT NULL
                         AND TRIM(p.inversiones) <> ''
                        THEN 1
                        ELSE 0
                    END
                ) AS total_con_inversion
            FROM pac p
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filtros['periodo'])) {
            $sql .= " AND p.periodo = :periodo";
            $params[':periodo'] = (int)$filtros['periodo'];
        }

        if (isset($filtros['ejecucion']) && $filtros['ejecucion'] !== '' && $filtros['ejecucion'] !== '0') {
            $sql .= " AND p.ejecucion = :ejecucion";
            $params[':ejecucion'] = (int)$filtros['ejecucion'];
        }

        if (!empty($filtros['obac'])) {
            $sql .= " AND p.obac = :obac";
            $params[':obac'] = (int)$filtros['obac'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = :estado";
            $params[':estado'] = (int)$filtros['estado'];
        }

        $st = $db->prepare($sql);
        $st->execute($params);

        $row = $st->fetch(PDO::FETCH_ASSOC);

        return [
            'total_pac'           => (int)($row['total_pac'] ?? 0),
            'total_estimado'      => (float)($row['total_estimado'] ?? 0),
            'total_certificado'   => (float)($row['total_certificado'] ?? 0),
            'total_con_inversion' => (int)($row['total_con_inversion'] ?? 0),
        ];
    }
}