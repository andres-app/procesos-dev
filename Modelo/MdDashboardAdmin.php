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
                tm.nombre AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            INNER JOIN tipo_mercado tm ON tm.id = p.tipo_mercado
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            AND tm.nombre IS NOT NULL
            AND TRIM(tm.nombre) <> ''
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
                m.nombre AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            INNER JOIN modalidad m ON m.id = p.modalidad
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            AND m.nombre IS NOT NULL
            AND TRIM(m.nombre) <> ''
            GROUP BY m.nombre
            ORDER BY monto DESC, total DESC, nombre ASC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTendenciaMensual(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                UPPER(COALESCE(NULLIF(TRIM(p.mesconvoca), ''), 'SIN MES')) AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY UPPER(COALESCE(NULLIF(TRIM(p.mesconvoca), ''), 'SIN MES'))
        ";

        $st = $db->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $ordenMeses = [
            'ENERO' => 1,
            'FEBRERO' => 2,
            'MARZO' => 3,
            'ABRIL' => 4,
            'MAYO' => 5,
            'JUNIO' => 6,
            'JULIO' => 7,
            'AGOSTO' => 8,
            'SEPTIEMBRE' => 9,
            'OCTUBRE' => 10,
            'NOVIEMBRE' => 11,
            'DICIEMBRE' => 12,
            'SIN MES' => 99,
        ];

        usort($rows, static function ($a, $b) use ($ordenMeses) {
            $oa = $ordenMeses[$a['nombre']] ?? 98;
            $ob = $ordenMeses[$b['nombre']] ?? 98;
            return $oa <=> $ob;
        });

        return $rows;
    }

    public static function obtenerTopDependencias(array $filtros = [], int $limit = 5): array
    {
        $db = db();

        $sql = "
            SELECT
                COALESCE(d.nombre, 'SIN DEPENDENCIA') AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            LEFT JOIN dependencia d ON d.id = p.dependencia
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY d.nombre
            ORDER BY monto DESC, total DESC, nombre ASC
            LIMIT " . (int)$limit;

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTopObac(array $filtros = [], int $limit = 5): array
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
            LIMIT " . (int)$limit;

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerAlertasGerenciales(array $filtros = []): array
    {
        $db = db();

        $baseSql = " FROM pac p WHERE 1=1 ";
        $params = self::buildWhere($baseSql, $filtros);

        $alertas = [];

        $sqlSinCertificado = "
            SELECT COUNT(*)
            {$baseSql}
            AND (p.certificado IS NULL OR p.certificado <= 0)
        ";
        $st = $db->prepare($sqlSinCertificado);
        $st->execute($params);
        $alertas[] = [
            'titulo' => 'PAC sin certificado',
            'valor'  => (int)$st->fetchColumn(),
            'tono'   => 'amber',
        ];

        $sqlObservados = "
            SELECT COUNT(*)
            FROM pac p
            LEFT JOIN estado e ON e.id = p.estado
            WHERE 1=1
        ";
        $paramsObs = self::buildWhere($sqlObservados, $filtros);
        $sqlObservados .= " AND UPPER(COALESCE(e.nombre, '')) LIKE '%OBSERV%' ";
        $st = $db->prepare($sqlObservados);
        $st->execute($paramsObs);
        $alertas[] = [
            'titulo' => 'PAC observados',
            'valor'  => (int)$st->fetchColumn(),
            'tono'   => 'rose',
        ];

        $sqlSinDependencia = "
            SELECT COUNT(*)
            {$baseSql}
            AND p.dependencia IS NULL
        ";
        $st = $db->prepare($sqlSinDependencia);
        $st->execute($params);
        $alertas[] = [
            'titulo' => 'PAC sin dependencia',
            'valor'  => (int)$st->fetchColumn(),
            'tono'   => 'slate',
        ];

        $sqlConInversion = "
            SELECT COUNT(*)
            {$baseSql}
            AND p.inversiones IS NOT NULL
            AND TRIM(p.inversiones) <> ''
        ";
        $st = $db->prepare($sqlConInversion);
        $st->execute($params);
        $alertas[] = [
            'titulo' => 'PAC con inversión',
            'valor'  => (int)$st->fetchColumn(),
            'tono'   => 'emerald',
        ];

        return $alertas;
    }

    public static function obtenerComparativoFinanciero(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                COALESCE(SUM(p.estimado), 0) AS total_estimado,
                COALESCE(SUM(p.certificado), 0) AS total_certificado,
                SUM(CASE WHEN COALESCE(p.certificado, 0) > 0 THEN 1 ELSE 0 END) AS pac_con_certificado,
                SUM(CASE WHEN COALESCE(p.certificado, 0) <= 0 THEN 1 ELSE 0 END) AS pac_sin_certificado
            FROM pac p
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $st = $db->prepare($sql);
        $st->execute($params);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        $estimado = (float)($row['total_estimado'] ?? 0);
        $certificado = (float)($row['total_certificado'] ?? 0);

        return [
            'total_estimado'       => $estimado,
            'total_certificado'    => $certificado,
            'brecha'               => max($estimado - $certificado, 0),
            'cobertura_pct'        => $estimado > 0 ? round(($certificado / $estimado) * 100, 1) : 0,
            'pac_con_certificado'  => (int)($row['pac_con_certificado'] ?? 0),
            'pac_sin_certificado'  => (int)($row['pac_sin_certificado'] ?? 0),
        ];
    }

    public static function obtenerPacCriticos(array $filtros = [], int $limit = 8): array
    {
        $db = db();

        $sql = "
            SELECT
                p.id,
                p.nopac,
                p.descripcion,
                p.estimado,
                p.certificado,
                p.mesconvoca,
                COALESCE(e.nombre, '')  AS obac_nombre,
                COALESCE(es.nombre, '') AS estado_nombre,
                COALESCE(d.nombre, '')  AS dependencia_nombre
            FROM pac p
            LEFT JOIN entidad e ON e.id = p.obac
            LEFT JOIN estado es ON es.id = p.estado
            LEFT JOIN dependencia d ON d.id = p.dependencia
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            AND (
                COALESCE(p.certificado, 0) <= 0
                OR p.dependencia IS NULL
                OR UPPER(COALESCE(es.nombre, '')) LIKE '%OBSERV%'
            )
            ORDER BY
                CASE
                    WHEN UPPER(COALESCE(es.nombre, '')) LIKE '%OBSERV%' THEN 1
                    WHEN COALESCE(p.certificado, 0) <= 0 THEN 2
                    WHEN p.dependencia IS NULL THEN 3
                    ELSE 4
                END,
                p.estimado DESC,
                p.id DESC
            LIMIT " . (int)$limit;

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerParticipacionSectorDefensa(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                CASE
                    WHEN p.ejecucion = 4 THEN 'ACFFAA'
                    ELSE 'RESTO'
                END AS grupo,
                COUNT(*) AS total_pac,
                COALESCE(SUM(p.estimado), 0) AS total_monto
            FROM pac p
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY
                CASE
                    WHEN p.ejecucion = 4 THEN 'ACFFAA'
                    ELSE 'RESTO'
                END
        ";

        $st = $db->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $acffaaPac   = 0;
        $acffaaMonto = 0.0;
        $restoPac    = 0;
        $restoMonto  = 0.0;

        foreach ($rows as $row) {
            $grupo = strtoupper(trim((string)($row['grupo'] ?? '')));

            if ($grupo === 'ACFFAA') {
                $acffaaPac   = (int)($row['total_pac'] ?? 0);
                $acffaaMonto = (float)($row['total_monto'] ?? 0);
            } else {
                $restoPac    = (int)($row['total_pac'] ?? 0);
                $restoMonto  = (float)($row['total_monto'] ?? 0);
            }
        }

        $totalPac   = $acffaaPac + $restoPac;
        $totalMonto = $acffaaMonto + $restoMonto;

        return [
            'acffaa_pac'       => $acffaaPac,
            'acffaa_monto'     => $acffaaMonto,
            'acffaa_pct_pac'   => $totalPac > 0 ? round(($acffaaPac / $totalPac) * 100, 1) : 0,
            'acffaa_pct_monto' => $totalMonto > 0 ? round(($acffaaMonto / $totalMonto) * 100, 1) : 0,

            'resto_pac'        => $restoPac,
            'resto_monto'      => $restoMonto,
            'resto_pct_pac'    => $totalPac > 0 ? round(($restoPac / $totalPac) * 100, 1) : 0,
            'resto_pct_monto'  => $totalMonto > 0 ? round(($restoMonto / $totalMonto) * 100, 1) : 0,

            'total_pac'        => $totalPac,
            'total_monto'      => $totalMonto,
        ];
    }

    public static function obtenerParticipacionPie(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                CASE
                    WHEN p.ejecucion = 4 THEN 'ACFFAA'
                    ELSE 'OBAC'
                END AS nombre,
                COUNT(*) AS total,
                COALESCE(SUM(p.estimado), 0) AS monto
            FROM pac p
            WHERE 1=1
        ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
            GROUP BY
                CASE
                    WHEN p.ejecucion = 4 THEN 'ACFFAA'
                    ELSE 'OBAC'
                END
            ORDER BY monto DESC, total DESC, nombre ASC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerResumenListasGenerales(array $filtros = []): array
    {
        $db = db();

        $sql = "
        SELECT
            COALESCE(l.nombre, 'SIN LISTA') AS lista,

            SUM(
                CASE
                    WHEN p.modalidad = 2 THEN 1
                    ELSE 0
                END
            ) AS individuales_cantidad,

            SUM(
                CASE
                    WHEN p.modalidad = 2 THEN COALESCE(p.estimado, 0)
                    ELSE 0
                END
            ) AS individuales_monto,

            SUM(
                CASE
                    WHEN p.modalidad = 1 THEN 1
                    ELSE 0
                END
            ) AS corporativos_cantidad,

            SUM(
                CASE
                    WHEN p.modalidad = 1 THEN COALESCE(p.estimado, 0)
                    ELSE 0
                END
            ) AS corporativos_monto,

            COUNT(*) AS total_cantidad,
            COALESCE(SUM(p.estimado), 0) AS total_monto

        FROM pac p
        LEFT JOIN listas l ON l.id = p.lista
        WHERE 1=1
    ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
        AND p.modalidad IN (1, 2)
        GROUP BY COALESCE(l.nombre, 'SIN LISTA')
        ORDER BY
            CASE
                WHEN UPPER(COALESCE(l.nombre, '')) = 'LCMN' THEN 1
                WHEN UPPER(COALESCE(l.nombre, '')) = 'LGCS' THEN 2
                WHEN UPPER(COALESCE(l.nombre, '')) = 'LGCE' THEN 3
                WHEN UPPER(COALESCE(l.nombre, '')) = 'LCME' THEN 4
                ELSE 99
            END,
            lista ASC
    ";

        $st = $db->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $resultado = [];
        $totales = [
            'lista' => 'TOTAL',
            'individuales_cantidad' => 0,
            'individuales_monto'    => 0.0,
            'corporativos_cantidad' => 0,
            'corporativos_monto'    => 0.0,
            'total_cantidad'        => 0,
            'total_monto'           => 0.0,
            'is_total'              => true,
        ];

        foreach ($rows as $row) {
            $item = [
                'lista' => (string)($row['lista'] ?? 'SIN LISTA'),
                'individuales_cantidad' => (int)($row['individuales_cantidad'] ?? 0),
                'individuales_monto'    => (float)($row['individuales_monto'] ?? 0),
                'corporativos_cantidad' => (int)($row['corporativos_cantidad'] ?? 0),
                'corporativos_monto'    => (float)($row['corporativos_monto'] ?? 0),
                'total_cantidad'        => (int)($row['total_cantidad'] ?? 0),
                'total_monto'           => (float)($row['total_monto'] ?? 0),
            ];

            $totales['individuales_cantidad'] += $item['individuales_cantidad'];
            $totales['individuales_monto']    += $item['individuales_monto'];
            $totales['corporativos_cantidad'] += $item['corporativos_cantidad'];
            $totales['corporativos_monto']    += $item['corporativos_monto'];
            $totales['total_cantidad']        += $item['total_cantidad'];
            $totales['total_monto']           += $item['total_monto'];

            $resultado[] = $item;
        }

        $resultado[] = $totales;

        return $resultado;
    }

    public static function obtenerResumenTipoCompraPorMercado(array $filtros = []): array
    {
        $db = db();

        $sql = "
        SELECT
            COALESCE(tm.nombre, 'SIN MERCADO') AS mercado,

            SUM(
                CASE
                    WHEN p.modalidad = 2 THEN 1
                    ELSE 0
                END
            ) AS individuales_cantidad,

            SUM(
                CASE
                    WHEN p.modalidad = 2 THEN COALESCE(p.estimado, 0)
                    ELSE 0
                END
            ) AS individuales_monto,

            SUM(
                CASE
                    WHEN p.modalidad = 1 THEN 1
                    ELSE 0
                END
            ) AS corporativos_cantidad,

            SUM(
                CASE
                    WHEN p.modalidad = 1 THEN COALESCE(p.estimado, 0)
                    ELSE 0
                END
            ) AS corporativos_monto,

            COUNT(*) AS total_cantidad,
            COALESCE(SUM(p.estimado), 0) AS total_monto

        FROM pac p
        LEFT JOIN tipo_mercado tm ON tm.id = p.tipo_mercado
        WHERE 1=1
    ";

        $params = self::buildWhere($sql, $filtros);

        $sql .= "
        AND p.modalidad IN (1, 2)
        GROUP BY COALESCE(tm.nombre, 'SIN MERCADO')
        ORDER BY
            CASE
                WHEN UPPER(COALESCE(tm.nombre, '')) = 'NACIONAL' THEN 1
                WHEN UPPER(COALESCE(tm.nombre, '')) = 'EXTRANJERO' THEN 2
                ELSE 99
            END,
            mercado ASC
    ";

        $st = $db->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $resultado = [];
        $totales = [
            'mercado' => 'TOTAL',
            'individuales_cantidad' => 0,
            'individuales_monto'    => 0.0,
            'corporativos_cantidad' => 0,
            'corporativos_monto'    => 0.0,
            'total_cantidad'        => 0,
            'total_monto'           => 0.0,
            'is_total'              => true,
        ];

        foreach ($rows as $row) {
            $item = [
                'mercado' => (string)($row['mercado'] ?? 'SIN MERCADO'),
                'individuales_cantidad' => (int)($row['individuales_cantidad'] ?? 0),
                'individuales_monto'    => (float)($row['individuales_monto'] ?? 0),
                'corporativos_cantidad' => (int)($row['corporativos_cantidad'] ?? 0),
                'corporativos_monto'    => (float)($row['corporativos_monto'] ?? 0),
                'total_cantidad'        => (int)($row['total_cantidad'] ?? 0),
                'total_monto'           => (float)($row['total_monto'] ?? 0),
            ];

            $totales['individuales_cantidad'] += $item['individuales_cantidad'];
            $totales['individuales_monto']    += $item['individuales_monto'];
            $totales['corporativos_cantidad'] += $item['corporativos_cantidad'];
            $totales['corporativos_monto']    += $item['corporativos_monto'];
            $totales['total_cantidad']        += $item['total_cantidad'];
            $totales['total_monto']           += $item['total_monto'];

            $resultado[] = $item;
        }

        $resultado[] = $totales;

        return $resultado;
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
