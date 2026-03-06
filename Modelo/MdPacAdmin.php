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
                p.fuente,
                p.estimado,
                p.periodo,
                p.created_at,

                COALESCE(e.nombre,'') AS obac_nombre,
                COALESCE(f.nombre,'') AS fuente_nombre

            FROM pac p
            LEFT JOIN entidad e ON e.id = p.obac
            LEFT JOIN fuente  f ON f.id = p.fuente
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

        /* ORDEN SOLO POR FECHA DE CREACIÓN */
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
                p.fuente,
                p.estimado,
                p.periodo,

                COALESCE(e.nombre,'') AS obac_nombre,
                COALESCE(f.nombre,'') AS fuente_nombre

            FROM pac p
            LEFT JOIN entidad e ON e.id = p.obac
            LEFT JOIN fuente  f ON f.id = p.fuente
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
                fuente,
                estimado,
                periodo
            ) VALUES (
                :nopac,
                :pn,
                :estado,
                :descripcion,
                :obac,
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
            ':obac'        => (int)($data['obac'] ?? 0),
            ':fuente'      => (int)($data['fuente'] ?? 0),
            ':estimado'    => (float)($data['estimado'] ?? 0),
            ':periodo'     => (int)($data['periodo'] ?? date('Y')),
        ]);
    }
}
