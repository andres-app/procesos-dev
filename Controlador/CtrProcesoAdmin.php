<?php
require_once __DIR__ . '/../Config/config.php'; // ✅ necesario
require_once __DIR__ . '/../Modelo/MdProceso.php';
require_once __DIR__ . '/../Modelo/MdActividad.php';

final class CtrProcesoAdmin
{
    public static function actividades(): void
    {
        require_once __DIR__ . '/../Config/config.php';
    
        try {
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) { http_response_code(400); echo "ID inválido"; return; }
    
            $proceso = MdProceso::obtener($id);
            if (!$proceso) { http_response_code(404); echo "Proceso no encontrado"; return; }
    
            $actividades = MdActividad::listarPorProceso($id) ?? [];
    
            require __DIR__ . '/../Vista/modulos/admin/actividades.php';
    
        } catch (Throwable $e) {
            http_response_code(500);
            echo "<pre style='white-space:pre-wrap'>".
                 "ERROR: ".$e->getMessage()."\n\n".
                 $e->getFile().":".$e->getLine()."\n\n".
                 $e->getTraceAsString().
                 "</pre>";
        }
    }
}