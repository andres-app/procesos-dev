<?php
require_once __DIR__ . '/../Modelo/MdProceso.php';

class CtrProceso
{
  public static function index(): void
  {
    $filtros = [
      'q' => $_GET['q'] ?? null,
      'estado' => $_GET['estado'] ?? null,
      'periodo' => $_GET['periodo'] ?? 2026,
      'anio_convocatoria' => $_GET['anio_convocatoria'] ?? null,
    ];

    $procesos = MdProceso::listar($filtros);

    require __DIR__ . '/../Vista/modulos/procesos.php';
  }
}