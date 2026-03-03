<?php
require_once __DIR__ . '/../Modelo/MdProceso.php';
require_once __DIR__ . '/../Modelo/MdActividadProceso.php';

class CtrActividades
{
  public static function show(): void
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

    $actividades = MdActividadProceso::listarPorProceso($id);

    require __DIR__ . '/../Vista/modulos/actividades.php';
  }
}