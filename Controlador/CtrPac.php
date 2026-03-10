<?php

require_once __DIR__ . '/../Modelo/MdPac.php';

class CtrPac
{
    public static function index(): void
    {
        $pacs = MdPac::listar();

        require_once __DIR__ . '/../Vista/modulos/pac.php';
    }
}