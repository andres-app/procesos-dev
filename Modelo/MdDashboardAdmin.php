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

        $params = self::buildWhere($sql, $filtros);

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

    public static function obtenerResumenPorEstado(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                COALESCE(e.nombre, 'SIN ESTADO') AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            LEFT JOIN estado e ON e.id = p.estado
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY e.nombre
            ORDER BY total DESC, monto DESC, nombre ASC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerResumenPorObac(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                COALESCE(e.nombre, 'SIN OBAC') AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            LEFT JOIN entidad e ON e.id = p.obac
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY e.nombre
            ORDER BY monto DESC, total DESC, nombre ASC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerResumenPorMercado(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                COALESCE(tm.nombre, 'SIN MERCADO') AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            LEFT JOIN tipo_mercado tm ON tm.id = p.tipo_mercado
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY tm.nombre
            ORDER BY monto DESC, total DESC, nombre ASC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerResumenPorModalidad(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                COALESCE(m.nombre, 'SIN MODALIDAD') AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            LEFT JOIN modalidad m ON m.id = p.modalidad
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY m.nombre
            ORDER BY monto DESC, total DESC, nombre ASC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function buildWhere(string &$sql, array $filtros = []): array
    {
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

        return $params;
    }
}