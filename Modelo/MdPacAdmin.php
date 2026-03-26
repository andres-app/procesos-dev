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
            'anio'                 => $anio,
            'fases_orden'          => $fasesOrden,
            'modalidades_por_fase' => $modalidadesPorFase,
            'obacs_orden'          => $obacsOrden,
            'detalle'              => $detalle,
            'subtotales'           => $subtotales,
            'totales'              => $totales,
            'valor_estimado_obac'  => $valorEstimadoPorObac,
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

    public static function importarDesdeCsv(string $tmpPath): array
    {
        $db = db();

        $fp = fopen($tmpPath, 'r');
        if (!$fp) {
            throw new Exception('No se pudo abrir el archivo CSV.');
        }

        $insertados = 0;
        $omitidos   = 0;
        $errores    = [];
        $filaNumero = 0;

        try {
            $delimiter = self::detectarSeparadorCsv($tmpPath);

            $db->beginTransaction();

            while (($row = fgetcsv($fp, 0, $delimiter)) !== false) {
                $filaNumero++;

                // limpiar BOM en primera celda
                if ($filaNumero === 1 && isset($row[0])) {
                    $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$row[0]);
                }

                // Ignorar cabecera
                if ($filaNumero === 1) {
                    continue;
                }

                $row = array_map(static function ($v) {
                    return trim((string)$v);
                }, $row);

                // Ignorar filas vacías
                if (count(array_filter($row, static fn($v) => $v !== '')) === 0) {
                    $omitidos++;
                    continue;
                }

                // Completar columnas faltantes
                $row = array_pad($row, 18, '');

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
                    $certificado
                ] = $row;

                $nopac       = trim($nopac);
                $descripcion = trim($descripcion);
                $pn          = strtoupper(trim($pn ?: 'NP'));

                if ($nopac === '' || $descripcion === '') {
                    $errores[] = "Fila {$filaNumero}: nopac y descripción son obligatorios.";
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
                    $errores[] = "Fila {$filaNumero}: " . $e->getMessage();
                    $omitidos++;
                    continue;
                }

                if (self::existePac($nopac, $obacId, $pn)) {
                    $errores[] = "Fila {$filaNumero}: ya existe un PAC con N° PAC '{$nopac}', OBAC '{$obacTexto}' y P/NP '{$pn}'.";
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
                            certificado
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
                            :certificado
                        )";

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

                try {
                    $st->execute();
                    $insertados++;
                } catch (Throwable $e) {
                    $errores[] = "Fila {$filaNumero}: error al insertar. " . $e->getMessage();
                    $omitidos++;
                }
            }

            $db->commit();

            return [
                'insertados' => $insertados,
                'omitidos'   => $omitidos,
                'errores'    => $errores,
            ];
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        } finally {
            fclose($fp);
        }
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
}
