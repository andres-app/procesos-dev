<?php
// Modelo/MdPacAdmin.php
require_once __DIR__ . '/../Config/config.php';

class MdPacAdmin
{
    public static function listar(array $filtros = []): array
    {
        $db = db();

        $modalidadExcluidaId = 4;

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
        p.inversiones,
        p.created_at,

        COALESCE(est.nombre, '') AS estado_nombre_pac,
        COALESCE(e.nombre, '')   AS obac_nombre,
        COALESCE(f.nombre, '')   AS fuente_nombre,
        COALESCE(s.nombre, '')   AS seleccion_nombre,
        COALESCE(pe.nombre, '')  AS periodo_nombre,
        COALESCE(li.nombre, '')  AS lista_nombre,
        COALESCE(ej.nombre, '')  AS ejecucion_nombre,
        COALESCE(m.nombre, '')   AS modalidad_nombre,
        COALESCE(d.nombre, '')   AS dependencia_nombre,
        COALESCE(tm.nombre, '')  AS tipo_mercado_nombre,
        COALESCE(r.nombre, '')   AS rubro_nombre,

        COALESCE(ta_ult.nombre, est.nombre, '')  AS estado_nombre,
        COALESCE(ta_ult.estado, est.nombre, '')  AS estado_codigo,
        ap_ult.fecha AS estado_fecha

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

    LEFT JOIN actividades_pac ap_ult
        ON ap_ult.id = (
            SELECT ap2.id
            FROM actividades_pac ap2
            WHERE ap2.pac_id = p.id
            ORDER BY ap2.fecha DESC, ap2.id DESC
            LIMIT 1
        )

    LEFT JOIN tipos_actividad ta_ult
        ON ta_ult.id = ap_ult.tipo_actividad_id

    WHERE 1=1
    ";

        $params = [];

        // =========================
        // FILTROS BASE
        // =========================

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
            est.nombre LIKE :q OR
            ta_ult.nombre LIKE :q OR
            ta_ult.estado LIKE :q OR
            m.nombre LIKE :q
        )";
            $params[':q'] = "%{$q}%";
        }

        // =========================
        // FILTRO ACFFAA / EJECUCIÓN
        // =========================

        if (isset($filtros['ejecucion']) && $filtros['ejecucion'] !== '' && $filtros['ejecucion'] !== '0') {

            $ej = $filtros['ejecucion'];

            if (ctype_digit((string)$ej)) {
                $sql .= " AND p.ejecucion = :ejecucion_id";
                $params[':ejecucion_id'] = (int)$ej;
            } else {
                $sql .= " AND UPPER(ej.nombre) = :ejecucion_nombre";
                $params[':ejecucion_nombre'] = mb_strtoupper(trim((string)$ej), 'UTF-8');
            }
        }

        // =========================
        // FILTROS FUNCIONALES
        // =========================

        if (!empty($filtros['inversiones'])) {
            $sql .= " AND p.inversiones IS NOT NULL AND TRIM(p.inversiones) <> ''";
        }

        if (!empty($filtros['vraem'])) {
            $sql .= " AND UPPER(p.descripcion) LIKE :vraem";
            $params[':vraem'] = '%VRAEM%';
        }

        // =========================
        // LÓGICA CLAVE (IMPORTANTE)
        // =========================

        if (!empty($filtros['modalidad_excluida'])) {
            // SOLO EXCLUIDOS
            $sql .= " AND p.modalidad = :modalidad_excluida";
            $params[':modalidad_excluida'] = $modalidadExcluidaId;
        } else {
            // OCULTAR EXCLUIDOS EN TODO LO DEMÁS
            $sql .= " AND (p.modalidad IS NULL OR p.modalidad <> :modalidad_excluida_hide)";
            $params[':modalidad_excluida_hide'] = $modalidadExcluidaId;
        }

        // =========================
        // ORDEN FINAL
        // =========================

        $sql .= " ORDER BY p.created_at DESC, p.id DESC";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
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
                p.rubro,
                p.inversiones
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
                rubro,
                inversiones
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
                :rubro,
                :inversiones
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
                rubro = :rubro,
                inversiones = :inversiones
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
            ':inversiones'  => trim((string)($data['inversiones'] ?? '')),
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
            p.inversiones,
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

    public static function obtenerResumenSituacion(?int $anio = null, int $ejecucion = 4): array
    {
        $db = db();
        $hoy = date('Y-m-d');

        $sql = "
            SELECT
                p.id,
                p.nopac,
                p.descripcion,
                p.estimado,
                p.mesconvoca,
                p.acta_inclu,
                p.inversiones,

                COALESCE(ob.nombre, '') AS obac_nombre,
                COALESCE(md.nombre, '') AS modalidad_nombre,
                COALESCE(es.nombre, '') AS estado_nombre,
                COALESCE(fu.nombre, '') AS fuente_nombre,
                COALESCE(se.nombre, '') AS seleccion_nombre,

                COUNT(DISTINCT pr.id) AS total_procesos

            FROM pac p
            LEFT JOIN entidad ob     ON ob.id = p.obac
            LEFT JOIN modalidad md   ON md.id = p.modalidad
            LEFT JOIN estado es      ON es.id = p.estado
            LEFT JOIN fuente fu      ON fu.id = p.fuente
            LEFT JOIN seleccion se   ON se.id = p.seleccion
            LEFT JOIN proceso_pac pp ON pp.pac_id = p.id
            LEFT JOIN procesos pr    ON pr.id = pp.proceso_id

            WHERE 1=1
            ";

        $params = [];

        if (!empty($anio)) {
            $sql .= " AND YEAR(p.created_at) = :anio";
            $params[':anio'] = (int)$anio;
        }

        // SOLO ACFFAA
        $sql .= " AND p.ejecucion = :ejecucion";
        $params[':ejecucion'] = $ejecucion;

        // NO CONSIDERAR EXCLUIDOS / MODALIDAD 4
        $sql .= " AND (p.modalidad IS NULL OR p.modalidad <> 4)";

        $sql .= "
        GROUP BY
            p.id,
            p.nopac,
            p.descripcion,
            p.estimado,
            p.mesconvoca,
            p.acta_inclu,
            p.inversiones,
            ob.nombre,
            md.nombre,
            es.nombre,
            fu.nombre,
            se.nombre
        ORDER BY
            p.id ASC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $pacIds = array_values(array_filter(array_map(
            static fn($r) => (int)($r['id'] ?? 0),
            $rows
        )));

        $actividadesPorPac = [];

        if (!empty($pacIds)) {
            $placeholders = implode(',', array_fill(0, count($pacIds), '?'));

            $sqlAct = "
        SELECT
            ap.id,
            ap.pac_id,
            ap.fecha,
            COALESCE(ap.comentario, '') AS comentario,
            COALESCE(ta.nombre, '') AS tipo_nombre,
            COALESCE(ta.estado, '') AS tipo_estado
        FROM actividades_pac ap
        LEFT JOIN tipos_actividad ta
            ON ta.id = ap.tipo_actividad_id
        WHERE ap.pac_id IN ($placeholders)
        ORDER BY ap.pac_id ASC, ap.fecha ASC, ap.id ASC
        ";

            $stAct = $db->prepare($sqlAct);
            $stAct->execute($pacIds);
            $acts = $stAct->fetchAll(PDO::FETCH_ASSOC) ?: [];

            foreach ($acts as $a) {
                $pacId = (int)($a['pac_id'] ?? 0);
                if ($pacId <= 0) {
                    continue;
                }

                if (!isset($actividadesPorPac[$pacId])) {
                    $actividadesPorPac[$pacId] = [];
                }

                $actividadesPorPac[$pacId][] = $a;
            }
        }

        $detalle = [];
        $subtotales = [];
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

        $valorObac = [
            'CCFFAA' => 0.0,
            'EP'     => 0.0,
            'FAP'    => 0.0,
            'MGP'    => 0.0,
            'CONIDA' => 0.0,
        ];

        $modalidadesPorFase = [];
        $fasesDetectadas = [];
        $detallePlano = [];

        foreach ($rows as $r) {
            $pacId = (int)($r['id'] ?? 0);
            $estado = (string)($r['estado_nombre'] ?? '');
            $fase = self::normalizarFaseResumen($estado);

            if ($fase === null) {
                continue;
            }

            $modalidad = self::normalizarModalidadResumen((string)($r['modalidad_nombre'] ?? ''));
            $obac = self::normalizarObacResumen((string)($r['obac_nombre'] ?? ''));

            if ($obac === null) {
                continue;
            }

            $estimado = (float)($r['estimado'] ?? 0);
            $procesos = (int)($r['total_procesos'] ?? 0);

            if (!in_array($fase, $fasesDetectadas, true)) {
                $fasesDetectadas[] = $fase;
            }

            if (!isset($modalidadesPorFase[$fase])) {
                $modalidadesPorFase[$fase] = [];
            }

            if (!in_array($modalidad, $modalidadesPorFase[$fase], true)) {
                $modalidadesPorFase[$fase][] = $modalidad;
            }

            if (!isset($detalle[$fase][$modalidad])) {
                $detalle[$fase][$modalidad] = [
                    'CCFFAA'      => 0,
                    'EP'          => 0,
                    'FAP'         => 0,
                    'MGP'         => 0,
                    'CONIDA'      => 0,
                    'EXPEDIENTES' => 0,
                    'PROCESOS'    => 0,
                    'ESTIMADO'    => 0.0,
                ];
            }

            $detalle[$fase][$modalidad][$obac]++;
            $detalle[$fase][$modalidad]['EXPEDIENTES']++;
            $detalle[$fase][$modalidad]['PROCESOS'] += $procesos;
            $detalle[$fase][$modalidad]['ESTIMADO'] += $estimado;

            if (!isset($subtotales[$fase])) {
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
            }

            $subtotales[$fase][$obac]++;
            $subtotales[$fase]['EXPEDIENTES']++;
            $subtotales[$fase]['PROCESOS'] += $procesos;
            $subtotales[$fase]['ESTIMADO'] += $estimado;

            $totales[$obac]++;
            $totales['EXPEDIENTES']++;
            $totales['PROCESOS'] += $procesos;
            $totales['ESTIMADO'] += $estimado;

            $valorObac[$obac] += $estimado;

            $actividadesPac = $actividadesPorPac[$pacId] ?? [];

            $historialPartes = [];

            // ACTA (solo si existe)
            $acta = trim((string)($r['acta_inclu'] ?? ''));
            if ($acta !== '') {
                $historialPartes[] = 'Acta ' . $acta . '.';
            }

            // INVERSION (solo si existe)
            $inv = trim((string)($r['inversiones'] ?? ''));
            if ($inv !== '') {
                $historialPartes[] = 'Inversion: ' . $inv . '.';
            }
            $situacion = '';

            $actividadVigente = null;
            $actividadVigenteFecha = null;
            $actividadVigenteId = 0;

            foreach ($actividadesPac as $a) {
                $fecha = trim((string)($a['fecha'] ?? ''));
                $tipoNombre = trim((string)($a['tipo_nombre'] ?? ''));
                $comentario = trim((string)($a['comentario'] ?? ''));

                $textoHistorial = '';
                if ($tipoNombre !== '') {
                    $textoHistorial .= $tipoNombre;
                }
                if ($comentario !== '') {
                    $textoHistorial .= ($textoHistorial !== '' ? ': ' : '') . $comentario;
                }
                if ($fecha !== '') {
                    $textoHistorial .= ($textoHistorial !== '' ? ' del ' : '') . self::formatearFechaReporte($fecha);
                }

                if ($textoHistorial !== '') {
                    $historialPartes[] = $textoHistorial . '.';
                }

                if ($fecha !== '' && $fecha <= $hoy) {
                    $aid = (int)($a['id'] ?? 0);

                    if (
                        $actividadVigente === null ||
                        $fecha > $actividadVigenteFecha ||
                        ($fecha === $actividadVigenteFecha && $aid > $actividadVigenteId)
                    ) {
                        $actividadVigente = $a;
                        $actividadVigenteFecha = $fecha;
                        $actividadVigenteId = $aid;
                    }
                }
            }

            if ($actividadVigente !== null) {
                $tipoNombre = trim((string)($actividadVigente['tipo_nombre'] ?? ''));
                $comentario = trim((string)($actividadVigente['comentario'] ?? ''));
                $fecha = trim((string)($actividadVigente['fecha'] ?? ''));

                $situacion = $tipoNombre;
                if ($comentario !== '') {
                    $situacion .= ($situacion !== '' ? ' ' : '') . $comentario;
                }
                if ($fecha !== '') {
                    $situacion .= ($situacion !== '' ? ' DEL ' : '') . self::formatearFechaReporte($fecha);
                }
            }

            // Mantener las hojas dinámicas exactamente como las espera tu Excel/PDF
            $tipoDetalle = self::clasificarTipoDetalleResumen((string)($r['modalidad_nombre'] ?? ''));

            if (!isset($detallePlano[$fase][$tipoDetalle])) {
                $detallePlano[$fase][$tipoDetalle] = [];
            }

            $detallePlano[$fase][$tipoDetalle][] = [
                'id'          => $pacId,
                'nopac'       => (string)($r['nopac'] ?? ''),
                'obac'        => (string)($r['obac_nombre'] ?? ''),
                'descripcion' => (string)($r['descripcion'] ?? ''),
                'estimado'    => $estimado,
                'fpc'         => (string)($r['mesconvoca'] ?? ''),
                'estado'      => $estado,
                'ff'          => (string)($r['fuente_nombre'] ?? ''),
                'tp'          => self::abreviarSeleccionReporte((string)($r['seleccion_nombre'] ?? '')),
                'situacion'   => $situacion,
                'historial'   => implode("\n", $historialPartes),
                'procesos'    => $procesos,
            ];
        }

        return [
            'anio'                 => $anio,
            'ejecucion'            => $ejecucion,
            'fases_orden'          => $fasesDetectadas,
            'modalidades_por_fase' => $modalidadesPorFase,
            'detalle'              => $detalle,
            'subtotales'           => $subtotales,
            'totales'              => $totales,
            'valor_estimado_obac'  => $valorObac,
            'detalle_plano'        => $detallePlano,
        ];
    }

    private static function clasificarTipoDetalleResumen(string $modalidadNombre): string
    {
        $txt = mb_strtoupper(trim($modalidadNombre), 'UTF-8');

        $corporativos = [
            'CORPORATIVO',
            'CORPORATIVOS',
            'COMPRA CORPORATIVA',
            'COMPRAS CORPORATIVAS',
        ];

        foreach ($corporativos as $valor) {
            if ($txt === $valor) {
                return 'Corporativo';
            }
        }

        return 'Individual';
    }

    private static function normalizarFaseResumen(string $estadoNombre): ?string
    {
        $txt = mb_strtoupper(trim($estadoNombre), 'UTF-8');

        if ($txt === '') {
            return null;
        }

        $txt = str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
            ['A', 'E', 'I', 'O', 'U', 'N'],
            $txt
        );

        $txt = preg_replace('/\s+/', ' ', $txt);
        $txt = trim((string)$txt);

        // FASE: NO RECEPCIONADOS
        if (
            strpos($txt, 'PUBLICADO') !== false ||
            strpos($txt, 'PUBLICADA') !== false ||
            strpos($txt, 'SOLICITADO') !== false ||
            strpos($txt, 'SOLICITADA') !== false ||
            strpos($txt, 'NO RECEPC') !== false ||
            strpos($txt, 'NORECEPC') !== false
        ) {
            return 'NO RECEPCIONADOS';
        }

        // FASE: OBSERVADOS
        if (
            strpos($txt, 'OBSERVADO') !== false ||
            strpos($txt, 'OBSERVADA') !== false ||
            strpos($txt, 'SUBSANADO') !== false ||
            strpos($txt, 'SUBSANADA') !== false
        ) {
            return 'OBSERVADOS';
        }

        // FASE: ESTUDIO DE MERCADO
        if (
            strpos($txt, 'ESTUDIO DE MERCADO') !== false ||
            (strpos($txt, 'ESTUDIO') !== false && strpos($txt, 'MERCADO') !== false)
        ) {
            return 'ESTUDIO DE MERCADO';
        }

        // FASE: PROCESO DE COMPRAS
        if (
            strpos($txt, 'PROCESO DE COMPRAS') !== false ||
            strpos($txt, 'PROCESO DE COMPRA') !== false ||
            strpos($txt, 'CONVOCADO') !== false ||
            strpos($txt, 'CONVOCADA') !== false ||
            strpos($txt, 'ADJUDICADO') !== false ||
            strpos($txt, 'ADJUDICADA') !== false ||
            strpos($txt, 'CONSENTIDO') !== false ||
            strpos($txt, 'CONSENTIDA') !== false ||
            strpos($txt, 'DESIERTO') !== false ||
            strpos($txt, 'DESIERTA') !== false
        ) {
            return 'PROCESO DE COMPRAS';
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

    private static function formatearFechaReporte(string $fecha): string
    {
        $fecha = trim($fecha);
        if ($fecha === '') {
            return '';
        }

        $ts = strtotime($fecha);
        if ($ts === false) {
            return $fecha;
        }

        return date('d/m/Y', $ts);
    }

    private static function abreviarSeleccionReporte(string $nombre): string
    {
        $txt = mb_strtoupper(trim($nombre), 'UTF-8');

        if ($txt === '') {
            return '';
        }

        $map = [
            'ADJUDICACION SIMPLIFICADA'   => 'AS',
            'COMPARACION DE PRECIOS'      => 'CPRE',
            'CONCURSO PUBLICO'            => 'CP',
            'LICITACION PUBLICA'          => 'LP',
            'SUBASTA INVERSA ELECTRONICA' => 'SIE',
            'CONTRATACION DIRECTA'        => 'CD',
            'REGIMEN ESPECIAL'            => 'RES',
        ];

        return $map[$txt] ?? $nombre;
    }

    public static function importarDesdeCsv(string $tmpPath): array
    {
        $db = db();

        $fp = fopen($tmpPath, 'r');
        if (!$fp) {
            return [
                'insertados' => 0,
                'omitidos'   => 0,
                'errores'    => ['No se pudo abrir el archivo CSV.'],
            ];
        }

        $insertados = 0;
        $omitidos   = 0;
        $errores    = [];
        $filaNumero = 0;

        try {
            $delimiter = self::detectarSeparadorCsv($tmpPath);

            if (!$db->inTransaction()) {
                $db->beginTransaction();
            }

            while (($row = fgetcsv($fp, 0, $delimiter)) !== false) {
                $filaNumero++;

                if ($filaNumero === 1 && isset($row[0])) {
                    $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$row[0]);
                }

                if ($filaNumero === 1) {
                    continue;
                }

                $row = array_map(static function ($v) {
                    return trim((string)$v);
                }, $row);

                if (count(array_filter($row, static fn($v) => $v !== '')) === 0) {
                    $omitidos++;
                    continue;
                }

                $row = array_pad($row, 19, '');

                [
                    $nopac,
                    $pn,
                    $descripcion,
                    $obacTexto,
                    $fuenteTexto,
                    $estadoTexto,
                    $estimado,
                    $seleccionTexto,
                    $listaTexto,
                    $modalidadTexto,
                    $tipoMercadoTexto,
                    $rubroTexto,
                    $ejecucionTexto,
                    $dependenciaTexto,
                    $mesconvoca,
                    $periodoTexto,
                    $cantidad,
                    $certificado,
                    $inversiones
                ] = $row;

                $nopac       = trim($nopac);
                $descripcion = self::asegurarUtf8(trim($descripcion));
                $obacTexto   = self::asegurarUtf8(trim($obacTexto));
                $fuenteTexto = self::asegurarUtf8(trim($fuenteTexto));
                $estadoTexto = self::asegurarUtf8(trim($estadoTexto));
                $seleccionTexto = self::asegurarUtf8(trim($seleccionTexto));
                $listaTexto = self::asegurarUtf8(trim($listaTexto));
                $modalidadTexto = self::asegurarUtf8(trim($modalidadTexto));
                $tipoMercadoTexto = self::asegurarUtf8(trim($tipoMercadoTexto));
                $rubroTexto = self::asegurarUtf8(trim($rubroTexto));
                $ejecucionTexto = self::asegurarUtf8(trim($ejecucionTexto));
                $dependenciaTexto = self::asegurarUtf8(trim($dependenciaTexto));
                $periodoTexto = self::asegurarUtf8(trim($periodoTexto));
                $pn = strtoupper(trim($pn ?: 'NP'));
                $inversiones = self::asegurarUtf8(trim($inversiones));

                if ($nopac === '' || $descripcion === '') {
                    $errores[] = self::asegurarUtf8("Fila {$filaNumero}: nopac y descripción son obligatorios.");
                    $omitidos++;
                    continue;
                }

                if (!in_array($pn, ['P', 'NP'], true)) {
                    $pn = 'NP';
                }

                $estimado    = self::normalizarDecimal($estimado);
                $certificado = self::normalizarDecimal($certificado);
                $cantidad    = self::normalizarEntero($cantidad);
                $mesconvoca  = self::normalizarMesConvocatoria($mesconvoca);

                try {
                    $estadoId      = self::buscarIdPorNombre('estado', $estadoTexto);
                    $obacId        = self::buscarIdPorNombre('obac', $obacTexto);
                    $fuenteId      = self::buscarIdPorNombre('fuente', $fuenteTexto);
                    $seleccionId   = self::buscarIdPorNombre('seleccion', $seleccionTexto);
                    $listaId       = self::buscarIdPorNombre('lista', $listaTexto);
                    $modalidadId   = self::buscarIdPorNombre('modalidad', $modalidadTexto);
                    $tipoMercadoId = self::buscarIdPorNombre('tipo_mercado', $tipoMercadoTexto);
                    $rubroId       = self::buscarIdPorNombre('rubro', $rubroTexto);
                    $ejecucionId   = self::buscarIdPorNombre('entidad', $ejecucionTexto);
                    $dependenciaId = self::buscarIdPorNombre('dependencia', $dependenciaTexto);
                    $periodoId     = self::buscarIdPorNombre('periodo', $periodoTexto);
                } catch (Throwable $e) {
                    $errores[] = self::asegurarUtf8("Fila {$filaNumero}: " . $e->getMessage());
                    $omitidos++;
                    continue;
                }

                if (self::existePac($nopac, $obacId, $pn)) {
                    $errores[] = self::asegurarUtf8("Fila {$filaNumero}: ya existe un PAC con N° PAC '{$nopac}', OBAC '{$obacTexto}' y P/NP '{$pn}'.");
                    $omitidos++;
                    continue;
                }

                $sql = "INSERT INTO pac (
                        nopac,
                        pn,
                        descripcion,
                        obac,
                        fuente,
                        estado,
                        estimado,
                        seleccion,
                        lista,
                        modalidad,
                        tipo_mercado,
                        rubro,
                        ejecucion,
                        dependencia,
                        mesconvoca,
                        periodo,
                        cantidad,
                        certificado,
                        inversiones
                    ) VALUES (
                        :nopac,
                        :pn,
                        :descripcion,
                        :obac,
                        :fuente,
                        :estado,
                        :estimado,
                        :seleccion,
                        :lista,
                        :modalidad,
                        :tipo_mercado,
                        :rubro,
                        :ejecucion,
                        :dependencia,
                        :mesconvoca,
                        :periodo,
                        :cantidad,
                        :certificado,
                        :inversiones
                    )";

                try {
                    $st = $db->prepare($sql);
                    $st->bindValue(':nopac', $nopac, PDO::PARAM_STR);
                    $st->bindValue(':pn', $pn, PDO::PARAM_STR);
                    $st->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
                    $st->bindValue(':obac', $obacId, $obacId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':fuente', $fuenteId, $fuenteId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':estado', $estadoId, $estadoId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':estimado', $estimado);
                    $st->bindValue(':seleccion', $seleccionId, $seleccionId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':lista', $listaId, $listaId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':modalidad', $modalidadId, $modalidadId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':tipo_mercado', $tipoMercadoId, $tipoMercadoId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':rubro', $rubroId, $rubroId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':ejecucion', $ejecucionId, $ejecucionId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':dependencia', $dependenciaId, $dependenciaId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':mesconvoca', $mesconvoca, $mesconvoca === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                    $st->bindValue(':periodo', $periodoId, $periodoId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $st->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
                    $st->bindValue(':certificado', $certificado);
                    $st->bindValue(':inversiones', $inversiones, $inversiones === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);

                    $st->execute();
                    $insertados++;
                } catch (Throwable $e) {
                    $errores[] = self::asegurarUtf8("Fila {$filaNumero}: error al insertar. " . $e->getMessage());
                    $omitidos++;
                    continue;
                }
            }

            if ($db->inTransaction()) {
                $db->commit();
            }

            return self::limpiarArrayUtf8([
                'insertados' => $insertados,
                'omitidos'   => $omitidos,
                'errores'    => $errores,
            ]);
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            return self::limpiarArrayUtf8([
                'insertados' => 0,
                'omitidos'   => 0,
                'errores'    => ['Error crítico: ' . $e->getMessage()],
            ]);
        } finally {
            if (is_resource($fp)) {
                fclose($fp);
            }
        }
    }

    private static function asegurarUtf8(?string $texto): string
    {
        $texto = (string)($texto ?? '');

        if ($texto === '') {
            return '';
        }

        if (mb_check_encoding($texto, 'UTF-8')) {
            return $texto;
        }

        $convertido = @mb_convert_encoding($texto, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        if ($convertido === false) {
            $convertido = @iconv('Windows-1252', 'UTF-8//IGNORE', $texto);
        }

        return is_string($convertido) ? $convertido : '';
    }

    private static function limpiarArrayUtf8(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = self::limpiarArrayUtf8($v);
            } elseif (is_string($v)) {
                $data[$k] = self::asegurarUtf8($v);
            }
        }

        return $data;
    }
    private static function detectarSeparadorCsv(string $tmpPath): string
    {
        $muestra = file_get_contents($tmpPath, false, null, 0, 4096);
        if ($muestra === false) {
            return ';';
        }

        $muestra = preg_replace('/^\xEF\xBB\xBF/', '', $muestra);
        $primeraLinea = strtok($muestra, "\r\n");
        $primeraLinea = (string)$primeraLinea;

        $conteos = [
            ';'  => substr_count($primeraLinea, ';'),
            ','  => substr_count($primeraLinea, ','),
            "\t" => substr_count($primeraLinea, "\t"),
        ];

        arsort($conteos);
        $sep = (string)array_key_first($conteos);

        return ($conteos[$sep] ?? 0) > 0 ? $sep : ';';
    }

    private static function normalizarDecimal($valor): float
    {
        $valor = trim((string)$valor);

        if ($valor === '') {
            return 0.00;
        }

        $valor = str_replace(['S/', 's/', ' '], '', $valor);

        // Si viene como 12.345,67 o 12345,67
        if (preg_match('/^\d{1,3}(\.\d{3})*,\d+$/', $valor) === 1) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } else {
            $valor = str_replace(',', '', $valor);
        }

        return (float)$valor;
    }

    private static function normalizarEntero($valor): int
    {
        $valor = trim((string)$valor);
        if ($valor === '') {
            return 0;
        }

        return (int)$valor;
    }

    private static function normalizarMesConvocatoria(string $valor): ?string
    {
        $valor = self::normalizarTextoImportacion($valor);

        if ($valor === '') {
            return null;
        }

        $map = [
            'ENERO'      => 'ENERO',
            'FEBRERO'    => 'FEBRERO',
            'MARZO'      => 'MARZO',
            'ABRIL'      => 'ABRIL',
            'MAYO'       => 'MAYO',
            'JUNIO'      => 'JUNIO',
            'JULIO'      => 'JULIO',
            'AGOSTO'     => 'AGOSTO',
            'SETIEMBRE'  => 'SEPTIEMBRE',
            'SEPTIEMBRE' => 'SEPTIEMBRE',
            'OCTUBRE'    => 'OCTUBRE',
            'NOVIEMBRE'  => 'NOVIEMBRE',
            'DICIEMBRE'  => 'DICIEMBRE',
        ];

        return $map[$valor] ?? strtoupper(trim($valor));
    }

    private static function buscarIdPorNombre(string $tipo, string $nombre): ?int
    {
        $nombreOriginal = trim((string)$nombre);
        if ($nombreOriginal === '') {
            return null;
        }

        $db = db();

        $map = [
            'estado'       => ['tabla' => 'estado',       'campo' => 'nombre', 'label' => 'Estado'],
            'obac'         => ['tabla' => 'entidad',      'campo' => 'nombre', 'label' => 'OBAC'],
            'fuente'       => ['tabla' => 'fuente',       'campo' => 'nombre', 'label' => 'Fuente'],
            'seleccion'    => ['tabla' => 'seleccion',    'campo' => 'nombre', 'label' => 'Selección'],
            'lista'        => ['tabla' => 'listas',       'campo' => 'nombre', 'label' => 'Lista'],
            'modalidad'    => ['tabla' => 'modalidad',    'campo' => 'nombre', 'label' => 'Modalidad'],
            'tipo_mercado' => ['tabla' => 'tipo_mercado', 'campo' => 'nombre', 'label' => 'Tipo de mercado'],
            'rubro'        => ['tabla' => 'rubro',        'campo' => 'nombre', 'label' => 'Rubro'],
            'entidad'      => ['tabla' => 'entidad',      'campo' => 'nombre', 'label' => 'Ejecución'],
            'dependencia'  => ['tabla' => 'dependencia',  'campo' => 'nombre', 'label' => 'Dependencia'],
            'periodo'      => ['tabla' => 'periodo',      'campo' => 'nombre', 'label' => 'Periodo'],
        ];

        if (!isset($map[$tipo])) {
            return null;
        }

        $cfg = $map[$tipo];

        $nombreNormalizado = self::resolverAliasImportacion($tipo, $nombreOriginal);
        $nombreNormalizado = self::normalizarTextoImportacion($nombreNormalizado);

        $st = $db->query("SELECT id, {$cfg['campo']} AS nombre FROM {$cfg['tabla']} ORDER BY {$cfg['campo']}");
        $items = $st->fetchAll(PDO::FETCH_ASSOC);

        // 1) PRIMERA PASADA: coincidencia exacta únicamente
        foreach ($items as $item) {
            $cat = self::normalizarTextoImportacion((string)$item['nombre']);

            if ($cat === $nombreNormalizado) {
                return (int)$item['id'];
            }
        }

        // 2) Si el valor es corto/código, NO permitir coincidencia parcial
        // evita que "RO" haga match con "D Y T RDR RO"
        if (mb_strlen($nombreNormalizado, 'UTF-8') <= 4) {
            throw new Exception($cfg['label'] . " no reconocido: '{$nombreOriginal}'.");
        }

        // 3) SEGUNDA PASADA: coincidencia parcial solo para textos largos
        foreach ($items as $item) {
            $cat = self::normalizarTextoImportacion((string)$item['nombre']);

            if ($nombreNormalizado !== '' && strpos($cat, $nombreNormalizado) !== false) {
                return (int)$item['id'];
            }

            if ($cat !== '' && strpos($nombreNormalizado, $cat) !== false) {
                return (int)$item['id'];
            }
        }

        throw new Exception($cfg['label'] . " no reconocido: '{$nombreOriginal}'.");
    }

    private static function normalizarTextoImportacion(string $texto): string
    {
        $texto = trim($texto);

        if ($texto === '') {
            return '';
        }

        $reemplazos = [
            'Á' => 'A',
            'À' => 'A',
            'Ä' => 'A',
            'Â' => 'A',
            'É' => 'E',
            'È' => 'E',
            'Ë' => 'E',
            'Ê' => 'E',
            'Í' => 'I',
            'Ì' => 'I',
            'Ï' => 'I',
            'Î' => 'I',
            'Ó' => 'O',
            'Ò' => 'O',
            'Ö' => 'O',
            'Ô' => 'O',
            'Ú' => 'U',
            'Ù' => 'U',
            'Ü' => 'U',
            'Û' => 'U',
            'á' => 'A',
            'à' => 'A',
            'ä' => 'A',
            'â' => 'A',
            'é' => 'E',
            'è' => 'E',
            'ë' => 'E',
            'ê' => 'E',
            'í' => 'I',
            'ì' => 'I',
            'ï' => 'I',
            'î' => 'I',
            'ó' => 'O',
            'ò' => 'O',
            'ö' => 'O',
            'ô' => 'O',
            'ú' => 'U',
            'ù' => 'U',
            'ü' => 'U',
            'û' => 'U',
            'Ñ' => 'N',
            'ñ' => 'N',
        ];

        $texto = strtr($texto, $reemplazos);
        $texto = strtoupper($texto);
        $texto = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        return trim((string)$texto);
    }

    private static function resolverAliasImportacion(string $tipo, string $texto): string
    {
        $valor = self::normalizarTextoImportacion($texto);

        $aliases = [
            'estado' => [
                'PUBICADO'                => 'PUBLICADO',
                'PUBLICADA'               => 'PUBLICADO',
                'PUBLICADO'               => 'PUBLICADO',

                'SOLICITADO'              => 'SOLICITADO',
                'SOLICITADA'              => 'SOLICITADO',

                'OBSERVADO'               => 'OBSERVADO',
                'OBSERVADA'               => 'OBSERVADO',
                'OBSERVADOS'              => 'OBSERVADO',

                'SUBSANADO'               => 'SUBSANADO',
                'SUBSANADA'               => 'SUBSANADO',

                'ESTUDIO MERCADO'         => 'ESTUDIO DE MERCADO',
                'ESTUDIO DE MERCADO'      => 'ESTUDIO DE MERCADO',
            ],

            'obac' => [
                'ACFFAA'                  => 'ACFFAA',
                'CCFFAA'                  => 'CCFFAA',
                'EP'                      => 'EP',
                'FAP'                     => 'FAP',
                'MGP'                     => 'MGP',
                'CONIDA'                  => 'CONIDA',
                'MINDEF'                  => 'MINDEF',

                'COMANDO CONJUNTO'        => 'CCFFAA',
                'COMANDO CONJUNTO DE LAS FUERZAS ARMADAS' => 'CCFFAA',

                'EJERCITO'                => 'EP',
                'EJERCITO DEL PERU'       => 'EP',

                'FUERZA AEREA'            => 'FAP',
                'FUERZA AEREA DEL PERU'   => 'FAP',

                'MARINA'                  => 'MGP',
                'MARINA DE GUERRA'        => 'MGP',
                'MARINA DE GUERRA DEL PERU' => 'MGP',
            ],

            'entidad' => [
                'ACFFAA'                  => 'ACFFAA',
                'CCFFAA'                  => 'CCFFAA',
                'EP'                      => 'EP',
                'FAP'                     => 'FAP',
                'MGP'                     => 'MGP',
                'CONIDA'                  => 'CONIDA',
                'MINDEF'                  => 'MINDEF',

                'COMANDO CONJUNTO'        => 'CCFFAA',
                'COMANDO CONJUNTO DE LAS FUERZAS ARMADAS' => 'CCFFAA',

                'EJERCITO'                => 'EP',
                'EJERCITO DEL PERU'       => 'EP',

                'FUERZA AEREA'            => 'FAP',
                'FUERZA AEREA DEL PERU'   => 'FAP',

                'MARINA'                  => 'MGP',
                'MARINA DE GUERRA'        => 'MGP',
                'MARINA DE GUERRA DEL PERU' => 'MGP',
            ],

            'fuente' => [
                'RO'                      => 'RO',
                'RECURSO ORDINARIO'       => 'RO',
                'RECURSOS ORDINARIOS'     => 'RO',

                'RDR'                     => 'RDR',
                'RECURSOS DIRECTAMENTE RECAUDADOS' => 'RDR',

                'RD'                      => 'RD',
                'DONACIONES Y TRANSFERENCIAS' => 'DYT',
                'DYT'                     => 'DYT',
                'D Y T'                   => 'DYT',
                'D Y T FP'                => 'D Y T FP',
                'D Y T RDR RO'            => 'D Y T RDR RO',
                'D Y T RDR RP'            => 'D Y T RDR RP',
                'D Y T RO'                => 'D Y T RO',
                'D Y T ROOC'              => 'D Y T ROOC',
                'RDR D Y T'               => 'RDR D Y T',
                'RDR FP'                  => 'RDR FP',
                'RDR ROOC'                => 'RDR ROOC',
                'RO FP'                   => 'RO FP',
                'RO RDR'                  => 'RO RDR',
                'RO ROOC'                 => 'RO ROOC',
                'RO ROOC'                 => 'RO ROOC',
                'ROOC'                    => 'ROOC',
                'ROOC RD'                 => 'ROOC RD',
                'VRAEM'                   => 'VRAEM',
            ],

            'seleccion' => [
                'ADJUDICACION SIMPLIFICADA'   => 'ADJUDICACION SIMPLIFICADA',
                'AS'                          => 'ADJUDICACION SIMPLIFICADA',

                'COMPARACION DE PRECIOS'      => 'COMPARACION DE PRECIOS',
                'CPRE'                        => 'COMPARACION DE PRECIOS',

                'CONCURSO PUBLICO'            => 'CONCURSO PUBLICO',
                'CP'                          => 'CONCURSO PUBLICO',

                'LICITACION PUBLICA'          => 'LICITACION PUBLICA',
                'LP'                          => 'LICITACION PUBLICA',

                'SUBASTA INVERSA ELECTRONICA' => 'SUBASTA INVERSA ELECTRONICA',
                'SIE'                         => 'SUBASTA INVERSA ELECTRONICA',

                'CONTRATACION DIRECTA'        => 'CONTRATACION DIRECTA',
                'CD'                          => 'CONTRATACION DIRECTA',

                'REGIMEN ESPECIAL'            => 'REGIMEN ESPECIAL',
                'RES'                         => 'REGIMEN ESPECIAL',
            ],

            'lista' => [
                'LCMN' => 'LCMN',
                'LGCE' => 'LGCE',
                'LGCS' => 'LGCS',
                'LCME' => 'LCME',
            ],

            'modalidad' => [
                'CORPORATIVA' => 'CORPORATIVO',
                'CORPORATIVO' => 'CORPORATIVO',
                'INDIVIDUAL'  => 'INDIVIDUAL',
            ],

            'tipo_mercado' => [
                'NACIONAL'           => 'NACIONAL',
                'MERCADO NACIONAL'   => 'NACIONAL',
                'EXTRANJERO'         => 'EXTRANJERO',
                'MERCADO EXTRANJERO' => 'EXTRANJERO',
            ],

            'rubro' => [
                'BIEN'               => 'BIEN',
                'BIENES'             => 'BIEN',
                'SERVICIO'           => 'SERVICIO',
                'SERVICIOS'          => 'SERVICIO',
                'OBRA'               => 'OBRA',
                'CONSULTORIA DE OBRA' => 'CONSULTORIA DE OBRA',
            ],

            'periodo' => [
                '2025' => '2025',
                '2026' => '2026',
                '2027' => '2027',
            ],
        ];

        return $aliases[$tipo][$valor] ?? $valor;
    }

    public static function actualizarEstado(int $pacId, int $estadoId, ?PDO $db = null): bool
    {
        $db = $db ?: db();

        $sql = "UPDATE pac SET estado = :estado WHERE id = :id";
        $st = $db->prepare($sql);

        return $st->execute([
            ':estado' => $estadoId,
            ':id'     => $pacId,
        ]);
    }

    public static function actualizarEstadoDesdeTipoActividad(int $pacId, int $tipoActividadId, ?PDO $db = null): bool
    {
        $db = $db ?: db();

        $sql = "
        UPDATE pac p
        INNER JOIN tipos_actividad ta
            ON ta.id = :tipo_actividad_id
        SET p.estado = ta.estado_id
        WHERE p.id = :pac_id
          AND ta.estado_id IS NOT NULL
    ";

        $st = $db->prepare($sql);

        return $st->execute([
            ':pac_id'            => $pacId,
            ':tipo_actividad_id' => $tipoActividadId,
        ]);
    }
}
