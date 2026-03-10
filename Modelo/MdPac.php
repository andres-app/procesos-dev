<?php

require_once __DIR__ . '/../Config/config.php';

class MdPac
{
    public static function listar(): array
    {
        $db = db();

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
            ORDER BY p.created_at DESC, p.id DESC
        ";

        $st = $db->prepare($sql);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}