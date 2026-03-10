<?php

require_once __DIR__ . '/../Modelo/MdPac.php';

class CtrPac
{
    public static function index(): void
    {
        $filtro = $_GET['f'] ?? 'acffaa';

        $pacs = MdPac::listar($filtro);

        require_once __DIR__ . '/../Vista/modulos/pac.php';
    }
}