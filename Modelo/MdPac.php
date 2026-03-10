<?php

require_once __DIR__ . '/../Config/config.php';

class MdPac
{
    public static function listar(string $filtro = 'acffaa'): array
    {
        $db = db();

        $where = '';

        if ($filtro === 'acffaa') {
            $where = "WHERE p.ejecucion = 4";
        }

        if ($filtro === 'inversiones') {
            $where = "WHERE p.inversiones LIKE 'CUI%'";
        }

        $sql = "
            SELECT
                p.id,
                p.nopac,
                p.pn,
                p.estado,
                p.descripcion,
                COALESCE(e.nombre, '') AS obac,
                COALESCE(s.nombre, '') AS seleccion_nombre,
                COALESCE(s.abreviacion, '') AS seleccion_abrev,
                COALESCE(p.estimado, 0) AS estimado,
                p.created_at
            FROM pac p
            LEFT JOIN entidad e ON e.id = p.obac
            LEFT JOIN seleccion s ON s.id = p.seleccion
            $where
            ORDER BY p.created_at DESC, p.id DESC
        ";

        $st = $db->prepare($sql);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contadores(): array
    {
        $db = db();

        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN ejecucion = 4 THEN 1 ELSE 0 END) AS acffaa,
                SUM(CASE WHEN inversiones LIKE 'CUI%' THEN 1 ELSE 0 END) AS inversiones
            FROM pac
        ";

        $st = $db->prepare($sql);
        $st->execute();

        return $st->fetch(PDO::FETCH_ASSOC);
    }
}