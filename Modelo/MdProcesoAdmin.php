<?php
// Modelo/MdProcesoAdmin.php
require_once __DIR__ . '/../Config/config.php';

class MdProcesoAdmin
{
    public static function listar(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                p.id,
                p.codigo_proceso,
                p.tipo_proceso,
                p.expediente,
                p.obac,
                p.descripcion,
                p.estimado,
                p.estado_id,
                p.anio_convocatoria,
                p.periodo,
                p.convocatoria,
                p.moneda,
                p.fecha_registro,
                p.objeto_contratacion,
                p.ganador,
                p.fecha_adjudicacion,
                p.fecha_consentido,
                p.created_at,
                p.updated_at,
                COALESCE(ep.nombre, '') AS estado_nombre,
                COALESCE(epro.nombre, '') AS obac_nombre,
                COUNT(DISTINCT pp.pac_id) AS total_pacs,
                COALESCE(
                    GROUP_CONCAT(
                        DISTINCT CONCAT(COALESCE(entPac.nombre, ''), '-', COALESCE(pacv.nopac, ''))
                        SEPARATOR ' | '
                    ),
                    ''
                ) AS obacs_involucrados
            FROM procesos p
            LEFT JOIN estado ep
                ON ep.id = p.estado_id
            LEFT JOIN entidad epro
                ON epro.id = p.obac
            LEFT JOIN proceso_pac pp
                ON pp.proceso_id = p.id
            LEFT JOIN pac pacv
                ON pacv.id = pp.pac_id
            LEFT JOIN entidad entPac
                ON entPac.id = pacv.obac
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filtros['periodo'])) {
            $sql .= " AND p.periodo = :periodo";
            $params[':periodo'] = (int)$filtros['periodo'];
        }

        if (!empty($filtros['estado_id'])) {
            $sql .= " AND p.estado_id = :estado_id";
            $params[':estado_id'] = (int)$filtros['estado_id'];
        }

        if (!empty($filtros['tipo_proceso'])) {
            $sql .= " AND p.tipo_proceso = :tipo_proceso";
            $params[':tipo_proceso'] = strtoupper(trim((string)$filtros['tipo_proceso']));
        }

        if (!empty($filtros['q'])) {
            $sql .= " AND (
                p.codigo_proceso LIKE :q OR
                p.expediente LIKE :q OR
                p.descripcion LIKE :q OR
                epro.nombre LIKE :q OR
                entPac.nombre LIKE :q
            )";
            $params[':q'] = '%' . trim((string)$filtros['q']) . '%';
        }

        $sql .= "
            GROUP BY
                p.id,
                p.codigo_proceso,
                p.tipo_proceso,
                p.expediente,
                p.obac,
                p.descripcion,
                p.estimado,
                p.estado_id,
                p.anio_convocatoria,
                p.periodo,
                p.convocatoria,
                p.moneda,
                p.fecha_registro,
                p.objeto_contratacion,
                p.ganador,
                p.fecha_adjudicacion,
                p.fecha_consentido,
                p.created_at,
                p.updated_at,
                ep.nombre,
                epro.nombre
            ORDER BY p.created_at DESC, p.id DESC
        ";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtener(int $id): ?array
    {
        $db = db();

        $sql = "
            SELECT
                p.*,
                COALESCE(ep.nombre, '') AS estado_nombre
            FROM procesos p
            LEFT JOIN estado ep
                ON ep.id = p.estado_id
            WHERE p.id = :id
            LIMIT 1
        ";

        $st = $db->prepare($sql);
        $st->execute([':id' => $id]);

        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function obtenerPacsVinculados(int $procesoId): array
    {
        $db = db();

        $sql = "
        SELECT
            p.id,
            p.nopac,
            p.pn,
            p.descripcion,
            p.estimado,
            COALESCE(e.nombre, '') AS obac_nombre,
            COALESCE(est.nombre, '') AS estado_nombre
        FROM proceso_pac pp
        INNER JOIN pac p
            ON p.id = pp.pac_id
        LEFT JOIN entidad e
            ON e.id = p.obac
        LEFT JOIN estado est
            ON est.id = p.estado
        WHERE pp.proceso_id = :proceso_id
        ORDER BY p.nopac ASC, p.id ASC
    ";

        $st = $db->prepare($sql);
        $st->execute([':proceso_id' => $procesoId]);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function listarPacsDisponibles(array $filtros = []): array
    {
        $db = db();

        $sql = "
            SELECT
                p.id,
                p.nopac,
                p.pn,
                p.descripcion,
                p.estimado,
                p.periodo,
                COALESCE(e.nombre, '') AS obac_nombre,
                COALESCE(est.nombre, '') AS estado_nombre,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM proceso_pac pp
                        INNER JOIN procesos pr ON pr.id = pp.proceso_id
                        WHERE pp.pac_id = p.id
                    ) THEN 1
                    ELSE 0
                END AS ya_vinculado
            FROM pac p
            LEFT JOIN entidad e
                ON e.id = p.obac
            LEFT JOIN estado est
                ON est.id = p.estado
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filtros['periodo'])) {
            $sql .= " AND p.periodo = :periodo";
            $params[':periodo'] = (int)$filtros['periodo'];
        }

        if (!empty($filtros['obac'])) {
            $sql .= " AND p.obac = :obac";
            $params[':obac'] = (int)$filtros['obac'];
        }

        if (!empty($filtros['q'])) {
            $sql .= " AND (
                p.nopac LIKE :q OR
                p.descripcion LIKE :q OR
                e.nombre LIKE :q
            )";
            $params[':q'] = '%' . trim((string)$filtros['q']) . '%';
        }

        $sql .= " ORDER BY p.created_at DESC, p.id DESC";

        $st = $db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function guardarConPacs(array $data, array $pacIds = []): int
    {
        $db = db();

        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
            }

            $sql = "
            INSERT INTO procesos (
                codigo_proceso,
                tipo_proceso,
                expediente,
                obac,
                descripcion,
                estimado,
                estado_id,
                anio_convocatoria,
                periodo,
                convocatoria,
                moneda,
                fecha_registro,
                objeto_contratacion,
                ganador,
                fecha_adjudicacion,
                fecha_consentido
            ) VALUES (
                :codigo_proceso,
                :tipo_proceso,
                :expediente,
                :obac,
                :descripcion,
                :estimado,
                :estado_id,
                :anio_convocatoria,
                :periodo,
                :convocatoria,
                :moneda,
                :fecha_registro,
                :objeto_contratacion,
                :ganador,
                :fecha_adjudicacion,
                :fecha_consentido
            )
        ";

            $st = $db->prepare($sql);
            $st->execute(self::mapData($data));

            $procesoId = (int)$db->lastInsertId();

            self::guardarPacsVinculados($procesoId, $pacIds, $db);

            require_once __DIR__ . '/MdActividadAdmin.php';

            $fechaActividad = !empty($data['convocatoria'])
                ? (string)$data['convocatoria']
                : date('Y-m-d');

            MdActividadAdmin::crearActividadInicialConvocado(
                $procesoId,
                $fechaActividad,
                'Registro inicial automático del proceso en estado CONVOCADO.',
                $db
            );

            if ($db->inTransaction()) {
                $db->commit();
            }

            return $procesoId;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function actualizarConPacs(int $id, array $data, array $pacIds = []): bool
    {
        $db = db();

        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
            }

            $sql = "
                UPDATE procesos SET
                    codigo_proceso       = :codigo_proceso,
                    tipo_proceso         = :tipo_proceso,
                    expediente           = :expediente,
                    obac                 = :obac,
                    descripcion          = :descripcion,
                    estimado             = :estimado,
                    estado_id            = :estado_id,
                    anio_convocatoria    = :anio_convocatoria,
                    periodo              = :periodo,
                    convocatoria         = :convocatoria,
                    moneda               = :moneda,
                    fecha_registro       = :fecha_registro,
                    objeto_contratacion  = :objeto_contratacion,
                    ganador              = :ganador,
                    fecha_adjudicacion   = :fecha_adjudicacion,
                    fecha_consentido     = :fecha_consentido
                WHERE id = :id
            ";

            $params = self::mapData($data);
            $params[':id'] = $id;

            $st = $db->prepare($sql);
            $ok = $st->execute($params);

            $del = $db->prepare("DELETE FROM proceso_pac WHERE proceso_id = :proceso_id");
            $del->execute([':proceso_id' => $id]);

            self::guardarPacsVinculados($id, $pacIds, $db);

            if ($db->inTransaction()) {
                $db->commit();
            }

            return $ok;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    private static function guardarPacsVinculados(int $procesoId, array $pacIds, PDO $db): void
    {
        $pacIds = array_values(array_unique(array_map('intval', $pacIds)));
        $pacIds = array_values(array_filter($pacIds, fn($id) => $id > 0));

        if (empty($pacIds)) {
            return;
        }

        foreach ($pacIds as $pacId) {
            if (self::pacYaVinculadoAOtroProceso($pacId, null, $db)) {
                throw new Exception("El PAC ID {$pacId} ya está vinculado a otro proceso.");
            }
        }

        $sql = "
            INSERT INTO proceso_pac (proceso_id, pac_id)
            VALUES (:proceso_id, :pac_id)
        ";

        $st = $db->prepare($sql);

        foreach ($pacIds as $pacId) {
            $st->execute([
                ':proceso_id' => $procesoId,
                ':pac_id'     => $pacId,
            ]);
        }
    }

    private static function pacYaVinculadoAOtroProceso(int $pacId, ?int $procesoIdExcluir = null, ?PDO $db = null): bool
    {
        $db = $db ?: db();

        $sql = "
            SELECT COUNT(*)
            FROM proceso_pac
            WHERE pac_id = :pac_id
        ";

        $params = [':pac_id' => $pacId];

        if (!empty($procesoIdExcluir)) {
            $sql .= " AND proceso_id <> :proceso_id_excluir";
            $params[':proceso_id_excluir'] = $procesoIdExcluir;
        }

        $st = $db->prepare($sql);
        $st->execute($params);

        return (int)$st->fetchColumn() > 0;
    }

    public static function listarEstadosProceso(): array
    {
        $db = db();
        $st = $db->query("SELECT id, nombre FROM estado ORDER BY id ASC");
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function existeCodigoProceso(string $codigo, ?int $ignoreId = null): bool
    {
        $db = db();

        $sql = "
            SELECT COUNT(*)
            FROM procesos
            WHERE codigo_proceso = :codigo
        ";

        $params = [
            ':codigo' => trim($codigo),
        ];

        if (!empty($ignoreId)) {
            $sql .= " AND id <> :ignore_id";
            $params[':ignore_id'] = $ignoreId;
        }

        $st = $db->prepare($sql);
        $st->execute($params);

        return (int)$st->fetchColumn() > 0;
    }

    private static function mapData(array $data): array
    {
        return [
            ':codigo_proceso'      => trim((string)($data['codigo_proceso'] ?? '')),
            ':tipo_proceso'        => strtoupper(trim((string)($data['tipo_proceso'] ?? 'INDIVIDUAL'))),
            ':expediente'          => self::nullIfEmpty($data['expediente'] ?? null),
            ':obac'                => self::nullIfEmpty($data['obac'] ?? null),
            ':descripcion'         => trim((string)($data['descripcion'] ?? '')),
            ':estimado'            => ($data['estimado'] ?? '') !== '' ? (float)$data['estimado'] : 0,
            ':estado_id'           => !empty($data['estado_id']) ? (int)$data['estado_id'] : null,
            ':anio_convocatoria'   => !empty($data['anio_convocatoria']) ? (int)$data['anio_convocatoria'] : null,
            ':periodo'             => !empty($data['periodo']) ? (int)$data['periodo'] : null,
            ':convocatoria'        => self::nullIfEmpty($data['convocatoria'] ?? null),
            ':moneda'              => self::nullIfEmpty($data['moneda'] ?? null),
            ':fecha_registro'      => self::nullIfEmpty($data['fecha_registro'] ?? null),
            ':objeto_contratacion' => self::nullIfEmpty($data['objeto_contratacion'] ?? null),
            ':ganador'             => self::nullIfEmpty($data['ganador'] ?? null),
            ':fecha_adjudicacion'  => self::nullIfEmpty($data['fecha_adjudicacion'] ?? null),
            ':fecha_consentido'    => self::nullIfEmpty($data['fecha_consentido'] ?? null),
        ];
    }

    private static function nullIfEmpty($value): ?string
    {
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }

    public static function obtenerIdEstadoPorCodigo(string $codigo): ?int
    {
        $db = db();

        $sql = "
        SELECT id
        FROM estado
        WHERE UPPER(nombre) = UPPER(:codigo)
        LIMIT 1
    ";

        $st = $db->prepare($sql);
        $st->execute([
            ':codigo' => trim($codigo),
        ]);

        $id = $st->fetchColumn();

        return $id ? (int)$id : null;
    }
}
