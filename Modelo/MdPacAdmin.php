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
                COALESCE(e.nombre, '')  AS obac_nombre,
                COALESCE(f.nombre, '')  AS fuente_nombre,
                COALESCE(s.nombre, '')  AS seleccion_nombre,
                COALESCE(pe.nombre, '') AS periodo_nombre,
                COALESCE(li.nombre, '') AS lista_nombre,
                COALESCE(ej.nombre, '') AS ejecucion_nombre,
                COALESCE(m.nombre, '')  AS modalidad_nombre,
                COALESCE(d.nombre, '')  AS dependencia_nombre,
                COALESCE(tm.nombre, '') AS tipo_mercado_nombre,
                COALESCE(r.nombre, '')  AS rubro_nombre
            FROM pac p
            LEFT JOIN entidad e         ON e.id = p.obac
            LEFT JOIN fuente f          ON f.id = p.fuente
            LEFT JOIN seleccion s       ON s.id = p.seleccion
            LEFT JOIN periodo pe        ON pe.id = p.periodo
            LEFT JOIN listas li         ON li.id = p.lista
            LEFT JOIN entidad ej        ON ej.id = p.ejecucion
            LEFT JOIN modalidad m       ON m.id = p.modalidad
            LEFT JOIN dependencia d     ON d.id = p.dependencia
            LEFT JOIN tipo_mercado tm   ON tm.id = p.tipo_mercado
            LEFT JOIN rubro r           ON r.id = p.rubro
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filtros['pn'])) {
            $sql .= " AND p.pn = :pn";
            $params[':pn'] = strtoupper(trim((string)$filtros['pn']));
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = :estado";
            $params[':estado'] = strtoupper(trim((string)$filtros['estado']));
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
                e.nombre LIKE :q
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
            ':estado'       => strtoupper(trim((string)($data['estado'] ?? 'PUBLICADO'))),
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
            ':mesconvoca' => !empty($data['mesconvoca']) ? trim((string)$data['mesconvoca']) : null,
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
}
