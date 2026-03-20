<?php
// Modelo/MdPacAdmin.php
require_once __DIR__ . '/../Config/config.php';

class MdPacAdmin
{
    public static function listar(array $filtros = []): array
    {
        $db = db();

        $sql = "
        SELECT
            p.id,
            p.nopac,
            p.pn,
            p.estado,
            p.descripcion,
            p.obac,
            p.seleccion,
            p.fuente,
            p.estimado,
            p.periodo,
            p.lista,
            p.ejecucion,
            p.modalidad,
            p.dependencia,
            p.mesconvoca,
            p.certificado,
            p.tipo_mercado,
            p.cantidad,
            p.rubro,
            p.created_at,

            COALESCE(est.nombre, '') AS estado_nombre,
            COALESCE(e.nombre, '')   AS obac_nombre,
            COALESCE(f.nombre, '')   AS fuente_nombre,
            COALESCE(s.nombre, '')   AS seleccion_nombre,
            COALESCE(pe.nombre, '')  AS periodo_nombre,
            COALESCE(li.nombre, '')  AS lista_nombre,
            COALESCE(ej.nombre, '')  AS ejecucion_nombre,
            COALESCE(m.nombre, '')   AS modalidad_nombre,
            COALESCE(d.nombre, '')   AS dependencia_nombre,
            COALESCE(tm.nombre, '')  AS tipo_mercado_nombre,
            COALESCE(r.nombre, '')   AS rubro_nombre
        FROM pac p
        LEFT JOIN estado est       ON est.id = p.estado
        LEFT JOIN entidad e        ON e.id = p.obac
        LEFT JOIN fuente f         ON f.id = p.fuente
        LEFT JOIN seleccion s      ON s.id = p.seleccion
        LEFT JOIN periodo pe       ON pe.id = p.periodo
        LEFT JOIN listas li        ON li.id = p.lista
        LEFT JOIN entidad ej       ON ej.id = p.ejecucion
        LEFT JOIN modalidad m      ON m.id = p.modalidad
        LEFT JOIN dependencia d    ON d.id = p.dependencia
        LEFT JOIN tipo_mercado tm  ON tm.id = p.tipo_mercado
        LEFT JOIN rubro r          ON r.id = p.rubro
        WHERE 1=1
    ";

        $params = [];

        if (!empty($filtros['pn'])) {
            $sql .= " AND p.pn = :pn";
            $params[':pn'] = strtoupper(trim((string)$filtros['pn']));
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = :estado";
            $params[':estado'] = (int)$filtros['estado'];
        }

        if (!empty($filtros['periodo'])) {
            $sql .= " AND p.periodo = :periodo";
            $params[':periodo'] = (int)$filtros['periodo'];
        }

        if (!empty($filtros['obac'])) {
            $sql .= " AND p.obac = :obac";
            $params[':obac'] = (int)$filtros['obac'];
        }

        if (!empty($filtros['q'])) {
            $q = trim((string)$filtros['q']);
            $sql .= " AND (
                p.nopac LIKE :q OR
                p.descripcion LIKE :q OR
                e.nombre LIKE :q OR
                est.nombre LIKE :q
            )";
            $params[':q'] = "%{$q}%";
        }

        $sql .= " ORDER BY p.created_at DESC, p.id DESC";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtener(int $id): ?array
    {
        $db = db();

        $sql = "
            SELECT
                p.id,
                p.nopac,
                p.pn,
                p.estado,
                p.descripcion,
                p.obac,
                p.seleccion,
                p.fuente,
                p.estimado,
                p.periodo,
                p.lista,
                p.ejecucion,
                p.modalidad,
                p.dependencia,
                p.mesconvoca,
                p.certificado,
                p.tipo_mercado,
                p.cantidad,
                p.rubro
            FROM pac p
            WHERE p.id = :id
            LIMIT 1
        ";

        $st = $db->prepare($sql);
        $st->execute([':id' => $id]);

        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function guardar(array $data): bool
    {
        $db = db();

        $sql = "
            INSERT INTO pac (
                nopac,
                pn,
                estado,
                descripcion,
                obac,
                seleccion,
                fuente,
                estimado,
                periodo,
                lista,
                ejecucion,
                modalidad,
                dependencia,
                mesconvoca,
                certificado,
                tipo_mercado,
                cantidad,
                rubro
            ) VALUES (
                :nopac,
                :pn,
                :estado,
                :descripcion,
                :obac,
                :seleccion,
                :fuente,
                :estimado,
                :periodo,
                :lista,
                :ejecucion,
                :modalidad,
                :dependencia,
                :mesconvoca,
                :certificado,
                :tipo_mercado,
                :cantidad,
                :rubro
            )
        ";

        $st = $db->prepare($sql);

        return $st->execute(self::mapData($data));
    }

    public static function actualizar(int $id, array $data): bool
    {
        $db = db();

        $sql = "
            UPDATE pac SET
                nopac = :nopac,
                pn = :pn,
                estado = :estado,
                descripcion = :descripcion,
                obac = :obac,
                seleccion = :seleccion,
                fuente = :fuente,
                estimado = :estimado,
                periodo = :periodo,
                lista = :lista,
                ejecucion = :ejecucion,
                modalidad = :modalidad,
                dependencia = :dependencia,
                mesconvoca = :mesconvoca,
                certificado = :certificado,
                tipo_mercado = :tipo_mercado,
                cantidad = :cantidad,
                rubro = :rubro
            WHERE id = :id
        ";

        $params = self::mapData($data);
        $params[':id'] = $id;

        $st = $db->prepare($sql);

        return $st->execute($params);
    }

    private static function mapData(array $data): array
    {
        return [
            ':nopac'        => trim((string)($data['nopac'] ?? '')),
            ':pn'           => strtoupper(trim((string)($data['pn'] ?? 'NP'))),
            ':estado'       => !empty($data['estado']) ? (int)$data['estado'] : null,
            ':descripcion'  => trim((string)($data['descripcion'] ?? '')),
            ':obac'         => !empty($data['obac']) ? (int)$data['obac'] : null,
            ':seleccion'    => !empty($data['seleccion']) ? (int)$data['seleccion'] : null,
            ':fuente'       => !empty($data['fuente']) ? (int)$data['fuente'] : null,
            ':estimado'     => ($data['estimado'] !== '' && $data['estimado'] !== null) ? (float)$data['estimado'] : 0,
            ':periodo'      => !empty($data['periodo']) ? (int)$data['periodo'] : null,
            ':lista'        => !empty($data['lista']) ? (int)$data['lista'] : null,
            ':ejecucion'    => !empty($data['ejecucion']) ? (int)$data['ejecucion'] : null,
            ':modalidad'    => !empty($data['modalidad']) ? (int)$data['modalidad'] : null,
            ':dependencia'  => !empty($data['dependencia']) ? (int)$data['dependencia'] : null,
            ':mesconvoca'   => !empty($data['mesconvoca']) ? trim((string)$data['mesconvoca']) : null,
            ':certificado'  => ($data['certificado'] !== '' && $data['certificado'] !== null) ? (float)$data['certificado'] : 0,
            ':tipo_mercado' => !empty($data['tipo_mercado']) ? (int)$data['tipo_mercado'] : null,
            ':cantidad'     => ($data['cantidad'] !== '' && $data['cantidad'] !== null) ? (int)$data['cantidad'] : 0,
            ':rubro'        => !empty($data['rubro']) ? (int)$data['rubro'] : null,
        ];
    }

    public static function existePac(string $nopac, ?int $obac, string $pn, ?int $ignoreId = null): bool
    {
        $db = db();

        $sql = "
            SELECT COUNT(*)
            FROM pac
            WHERE nopac = :nopac
              AND obac = :obac
              AND pn = :pn
        ";

        $params = [
            ':nopac' => trim($nopac),
            ':obac'  => $obac,
            ':pn'    => strtoupper(trim($pn)),
        ];

        if (!empty($ignoreId)) {
            $sql .= " AND id <> :ignoreId";
            $params[':ignoreId'] = $ignoreId;
        }

        $st = $db->prepare($sql);
        $st->execute($params);

        return (int)$st->fetchColumn() > 0;
    }

    public static function listarObac(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM entidad ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarFuente(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM fuente ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarSeleccion(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM seleccion ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPeriodo(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM periodo ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarListas(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM listas ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarEntidades(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM entidad ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarModalidades(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM modalidad ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarDependencias(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM dependencia ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarTiposMercado(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM tipo_mercado ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarRubros(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM rubro ORDER BY nombre");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerDetalle(int $id): ?array
    {
        $db = db();

        $sql = "
        SELECT
            p.id,
            p.nopac,
            p.pn,
            p.estado,
            p.descripcion,
            p.obac,
            p.seleccion,
            p.fuente,
            p.estimado,
            p.periodo,
            p.lista,
            p.ejecucion,
            p.modalidad,
            p.dependencia,
            p.mesconvoca,
            p.certificado,
            p.tipo_mercado,
            p.cantidad,
            p.rubro,
            p.created_at,

            COALESCE(est.nombre, '') AS estado_nombre,
            COALESCE(e.nombre, '')   AS obac_nombre,
            COALESCE(f.nombre, '')   AS fuente_nombre,
            COALESCE(s.nombre, '')   AS seleccion_nombre,
            COALESCE(pe.nombre, '')  AS periodo_nombre,
            COALESCE(li.nombre, '')  AS lista_nombre,
            COALESCE(ej.nombre, '')  AS ejecucion_nombre,
            COALESCE(m.nombre, '')   AS modalidad_nombre,
            COALESCE(d.nombre, '')   AS dependencia_nombre,
            COALESCE(tm.nombre, '')  AS tipo_mercado_nombre,
            COALESCE(r.nombre, '')   AS rubro_nombre
        FROM pac p
        LEFT JOIN estado est       ON est.id = p.estado
        LEFT JOIN entidad e        ON e.id = p.obac
        LEFT JOIN fuente f         ON f.id = p.fuente
        LEFT JOIN seleccion s      ON s.id = p.seleccion
        LEFT JOIN periodo pe       ON pe.id = p.periodo
        LEFT JOIN listas li        ON li.id = p.lista
        LEFT JOIN entidad ej       ON ej.id = p.ejecucion
        LEFT JOIN modalidad m      ON m.id = p.modalidad
        LEFT JOIN dependencia d    ON d.id = p.dependencia
        LEFT JOIN tipo_mercado tm  ON tm.id = p.tipo_mercado
        LEFT JOIN rubro r          ON r.id = p.rubro
        WHERE p.id = :id
        LIMIT 1
    ";

        $st = $db->prepare($sql);
        $st->execute([':id' => $id]);

        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function eliminar(int $id): bool
    {
        $db = db();

        $st = $db->prepare("DELETE FROM pac WHERE id = :id");
        return $st->execute([':id' => $id]);
    }

    public static function listarEstados(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM estado ORDER BY id");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerResumenSituacion(?int $anio = null): array
    {
        $db = db();

        $sql = "
            SELECT
                p.id,
                p.nopac,
                p.estimado,
                p.created_at,
                COALESCE(ob.nombre, '') AS obac_nombre,
                COALESCE(md.nombre, '') AS modalidad_nombre,
                COALESCE(es.nombre, '') AS estado_nombre
            FROM pac p
            LEFT JOIN entidad ob   ON ob.id = p.obac
            LEFT JOIN modalidad md ON md.id = p.modalidad
            LEFT JOIN estado es    ON es.id = p.estado
            WHERE 1=1
        ";

        $params = [];

        if (!empty($anio)) {
            $sql .= " AND YEAR(p.created_at) = :anio";
            $params[':anio'] = (int)$anio;
        }

        $sql .= " ORDER BY p.id ASC";

        $st = $db->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $fasesOrden = [
            'NORECEPCIONADOS',
            'OBSERVADOS',
            'ESTUDIO DE MERCADO',
        ];

        $modalidadesPorFase = [
            'NORECEPCIONADOS'     => ['Corporativo', 'Individual'],
            'OBSERVADOS'          => ['Corporativo', 'Individual'],
            'ESTUDIO DE MERCADO'  => ['Individual'],
        ];

        $obacsOrden = ['CCFFAA', 'EP', 'FAP', 'MGP', 'CONIDA'];

        $detalle = [];
        foreach ($fasesOrden as $fase) {
            $detalle[$fase] = [];
            foreach ($modalidadesPorFase[$fase] as $modalidad) {
                $detalle[$fase][$modalidad] = [
                    'CCFFAA'      => 0,
                    'EP'          => 0,
                    'FAP'         => 0,
                    'MGP'         => 0,
                    'CONIDA'      => 0,
                    'EXPEDIENTES' => 0,
                    'PROCESOS'    => 0,
                    'ESTIMADO'    => 0.0,
                    '_nopac'      => [],
                ];
            }
        }

        foreach ($rows as $row) {
            $fase = self::normalizarFaseResumen((string)($row['estado_nombre'] ?? ''));
            if ($fase === null) {
                continue;
            }

            $modalidad = self::normalizarModalidadResumen((string)($row['modalidad_nombre'] ?? ''));
            if (!isset($detalle[$fase][$modalidad])) {
                continue;
            }

            $obac = self::normalizarObacResumen((string)($row['obac_nombre'] ?? ''));

            if ($obac !== null) {
                $detalle[$fase][$modalidad][$obac]++;
            }

            $detalle[$fase][$modalidad]['EXPEDIENTES']++;
            $detalle[$fase][$modalidad]['ESTIMADO'] += (float)($row['estimado'] ?? 0);

            $nopacKey = trim((string)($row['nopac'] ?? ''));
            if ($nopacKey === '') {
                $nopacKey = 'ID-' . (string)$row['id'];
            }

            $detalle[$fase][$modalidad]['_nopac'][$nopacKey] = true;
        }

        foreach ($detalle as $fase => $mods) {
            foreach ($mods as $modalidad => $vals) {
                $detalle[$fase][$modalidad]['PROCESOS'] = count($vals['_nopac']);
                unset($detalle[$fase][$modalidad]['_nopac']);
            }
        }

        $subtotales = [];
        foreach ($fasesOrden as $fase) {
            $subtotales[$fase] = [
                'CCFFAA'      => 0,
                'EP'          => 0,
                'FAP'         => 0,
                'MGP'         => 0,
                'CONIDA'      => 0,
                'EXPEDIENTES' => 0,
                'PROCESOS'    => 0,
                'ESTIMADO'    => 0.0,
            ];

            foreach ($detalle[$fase] as $modalidad => $vals) {
                foreach (['CCFFAA', 'EP', 'FAP', 'MGP', 'CONIDA', 'EXPEDIENTES', 'PROCESOS'] as $k) {
                    $subtotales[$fase][$k] += (int)$vals[$k];
                }
                $subtotales[$fase]['ESTIMADO'] += (float)$vals['ESTIMADO'];
            }
        }

        $totales = [
            'CCFFAA'      => 0,
            'EP'          => 0,
            'FAP'         => 0,
            'MGP'         => 0,
            'CONIDA'      => 0,
            'EXPEDIENTES' => 0,
            'PROCESOS'    => 0,
            'ESTIMADO'    => 0.0,
        ];

        foreach ($subtotales as $sub) {
            foreach (['CCFFAA', 'EP', 'FAP', 'MGP', 'CONIDA', 'EXPEDIENTES', 'PROCESOS'] as $k) {
                $totales[$k] += (int)$sub[$k];
            }
            $totales['ESTIMADO'] += (float)$sub['ESTIMADO'];
        }

        $valorEstimadoPorObac = [
            'CCFFAA' => 0.0,
            'EP'     => 0.0,
            'FAP'    => 0.0,
            'MGP'    => 0.0,
            'CONIDA' => 0.0,
        ];

        foreach ($rows as $row) {
            $fase = self::normalizarFaseResumen((string)($row['estado_nombre'] ?? ''));
            if ($fase === null) {
                continue;
            }

            $obac = self::normalizarObacResumen((string)($row['obac_nombre'] ?? ''));
            if ($obac !== null) {
                $valorEstimadoPorObac[$obac] += (float)($row['estimado'] ?? 0);
            }
        }

        return [
            'anio'                => $anio,
            'fases_orden'         => $fasesOrden,
            'modalidades_por_fase'=> $modalidadesPorFase,
            'obacs_orden'         => $obacsOrden,
            'detalle'             => $detalle,
            'subtotales'          => $subtotales,
            'totales'             => $totales,
            'valor_estimado_obac' => $valorEstimadoPorObac,
        ];
    }

    private static function normalizarFaseResumen(string $estadoNombre): ?string
    {
        $txt = mb_strtoupper(trim($estadoNombre), 'UTF-8');

        if ($txt === '') {
            return null;
        }

        if (strpos($txt, 'NO RECEPC') !== false || strpos($txt, 'NORECEPC') !== false) {
            return 'NORECEPCIONADOS';
        }

        if (strpos($txt, 'OBSERV') !== false) {
            return 'OBSERVADOS';
        }

        if (strpos($txt, 'ESTUDIO') !== false && strpos($txt, 'MERCADO') !== false) {
            return 'ESTUDIO DE MERCADO';
        }

        return null;
    }

    private static function normalizarModalidadResumen(string $modalidadNombre): string
    {
        $txt = mb_strtoupper(trim($modalidadNombre), 'UTF-8');

        if (strpos($txt, 'CORPORAT') !== false) {
            return 'Corporativo';
        }

        return 'Individual';
    }

    private static function normalizarObacResumen(string $obacNombre): ?string
    {
        $txt = mb_strtoupper(trim($obacNombre), 'UTF-8');

        if ($txt === '') {
            return null;
        }

        if (strpos($txt, 'CCFFAA') !== false || strpos($txt, 'COMANDO CONJUNTO') !== false) {
            return 'CCFFAA';
        }

        if ($txt === 'EP' || strpos($txt, 'EJERCITO') !== false) {
            return 'EP';
        }

        if ($txt === 'FAP' || strpos($txt, 'FUERZA AEREA') !== false) {
            return 'FAP';
        }

        if ($txt === 'MGP' || strpos($txt, 'MARINA') !== false) {
            return 'MGP';
        }

        if (strpos($txt, 'CONIDA') !== false) {
            return 'CONIDA';
        }

        return null;
    }
}