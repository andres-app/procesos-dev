<?php

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
                p.created_at,
                COALESCE(e.nombre, '') AS obac_nombre,
                COALESCE(f.nombre, '') AS fuente_nombre
            FROM pac p
            LEFT JOIN entidad e ON e.id = p.obac
            LEFT JOIN fuente f ON f.id = p.fuente
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

        $sql .= " ORDER BY p.created_at DESC";

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
                COALESCE(e.nombre, '') AS obac_nombre,
                COALESCE(f.nombre, '') AS fuente_nombre
            FROM pac p
            LEFT JOIN entidad e ON e.id = p.obac
            LEFT JOIN fuente f ON f.id = p.fuente
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

        $sql = "INSERT INTO pac (
            nopac,
            pn,
            estado,
            descripcion,
            obac,
            seleccion,
            fuente,
            estimado,
            periodo
        ) VALUES (
            :nopac,
            :pn,
            :estado,
            :descripcion,
            :obac,
            :seleccion,
            :fuente,
            :estimado,
            :periodo
        )";

        $st = $db->prepare($sql);

        return $st->execute([
            ':nopac'       => trim((string)($data['nopac'] ?? '')),
            ':pn'          => strtoupper(trim((string)($data['pn'] ?? 'NP'))),
            ':estado'      => strtoupper(trim((string)($data['estado'] ?? 'PUBLICADO'))),
            ':descripcion' => trim((string)($data['descripcion'] ?? '')),
            ':obac'        => !empty($data['obac']) ? (int)$data['obac'] : null,
            ':seleccion'   => !empty($data['seleccion']) ? (int)$data['seleccion'] : null,
            ':fuente'      => !empty($data['fuente']) ? (int)$data['fuente'] : null,
            ':estimado'    => (float)($data['estimado'] ?? 0),
            ':periodo'     => !empty($data['periodo']) ? (int)$data['periodo'] : null,
        ]);
    }

    public static function existePac(string $nopac, ?int $obac, string $pn): bool
    {
        $db = db();

        $sql = "SELECT COUNT(*)
                FROM pac
                WHERE nopac = :nopac
                  AND obac = :obac
                  AND pn = :pn";

        $st = $db->prepare($sql);
        $st->execute([
            ':nopac' => trim($nopac),
            ':obac'  => $obac,
            ':pn'    => strtoupper(trim($pn)),
        ]);

        return (int)$st->fetchColumn() > 0;
    }

    public static function listarObac(): array
    {
        $db = db();

        $sql = "SELECT id, nombre FROM entidad ORDER BY nombre";

        $st = $db->prepare($sql);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarFuente(): array
    {
        $db = db();

        $sql = "SELECT id, nombre FROM fuente ORDER BY nombre";

        $st = $db->prepare($sql);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarSeleccion(): array
    {
        $db = db();

        $sql = "SELECT id, nombre FROM seleccion ORDER BY nombre";

        $st = $db->prepare($sql);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPeriodo(): array
    {
        $db = db();

        $sql = "SELECT id, nombre FROM periodo ORDER BY nombre";

        $st = $db->prepare($sql);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}