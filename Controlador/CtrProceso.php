<?php
require_once __DIR__ . '/../Modelo/MdProceso.php';
require_once __DIR__ . '/../Modelo/MdActividad.php'; // crea este modelo

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

public static function detalle(): void
{
  $id = (int)($_GET['id'] ?? 0);
  if ($id <= 0) {
    http_response_code(400);
    echo "ID inválido";
    return;
  }

  $proceso = MdProceso::obtener($id);
  if (!$proceso) {
    http_response_code(404);
    echo "Proceso no encontrado";
    return;
  }

  $actividades = MdActividad::listarPorProceso($id) ?? [];

  // ✅ ESTE ES TU ARCHIVO REAL
  require __DIR__ . '/../Vista/admin/procesos/actividades.php'; // ajusta carpeta exacta si difiere
}
}