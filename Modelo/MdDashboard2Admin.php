<?php
// Modelo/MdDashboard2Admin.php

require_once __DIR__ . '/MdDashboardAdmin.php';
require_once __DIR__ . '/MdPacAdmin.php';

class MdDashboard2Admin extends MdDashboardAdmin
{
    public static function obtenerDataDashboard2(array $filtros): array
    {
        $listasRaw   = self::obtenerResumenListasGenerales($filtros);
        $mercadosRaw = self::obtenerResumenTipoCompraPorMercado($filtros);
        $obacRaw     = self::obtenerResumenPorObac($filtros);

        $obacFormateado = self::formatearObac($obacRaw);

        return [
            'anio'       => self::obtenerAnioDashboard2($filtros),
            'total_pacs' => array_sum(array_column($obacFormateado, 'valor')),
            'listas'     => self::formatearListas($listasRaw),
            'mercados'   => self::formatearMercados($mercadosRaw),
            'obac'       => $obacFormateado,
        ];
    }

    private static function obtenerAnioDashboard2(array $filtros): int
    {
        $periodos = MdPacAdmin::listarPeriodo();

        foreach ($periodos as $p) {
            if ((string)($p['id'] ?? '') === (string)($filtros['periodo'] ?? '')) {
                return (int)($p['nombre'] ?? 2026);
            }
        }

        return 2026;
    }

    private static function num(array $row, array $keys): float
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return (float)$row[$key];
            }
        }

        return 0;
    }

    private static function txt(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string)$row[$key]) !== '') {
                return strtoupper(trim((string)$row[$key]));
            }
        }

        return '';
    }

private static function formatearListas(array $resumen): array
{
    // IDs reales según tu tabla listas
    $mapListas = [
        2 => 'LGCSM', // LGCS
        3 => 'LGCGG', // LGCE
        1 => 'LECMNE', // LCMN
        4 => 'LECMNE', // LCME
    ];

    $base = [
        'LGCSM' => [
            'codigo' => 'LGCSM',
            'descripcion' => 'De Carácter Secreto',
            'total' => 0,
            'individuales' => 0,
            'corporativos' => 0,
        ],
        'LGCGG' => [
            'codigo' => 'LGCGG',
            'descripcion' => 'Gobierno a Gobierno',
            'total' => 0,
            'individuales' => 0,
            'corporativos' => 0,
        ],
        'LECMNE' => [
            'codigo' => 'LECMNE',
            'descripcion' => 'Estratégicas (Nac. y Ext.)',
            'total' => 0,
            'individuales' => 0,
            'corporativos' => 0,
        ],
    ];

    foreach ($resumen as $r) {

        // 🔥 CLAVE: tomar ID real de lista
        $listaId = (int)($r['lista'] ?? $r['lista_id'] ?? 0);

        if (!isset($mapListas[$listaId])) {
            continue;
        }

        $codigo = $mapListas[$listaId];

        $individuales = (int)self::num($r, [
            'individuales','individual','total_individuales','cantidad_individual'
        ]);

        $corporativos = (int)self::num($r, [
            'corporativos','corporativo','total_corporativos','cantidad_corporativo'
        ]);

        $total = (int)self::num($r, [
            'total','cantidad','total_pacs'
        ]);

        if ($total === 0) {
            $total = $individuales + $corporativos;
        }

        // 🔥 SUMA (NO REEMPLAZA)
        $base[$codigo]['total'] += $total;
        $base[$codigo]['individuales'] += $individuales;
        $base[$codigo]['corporativos'] += $corporativos;
    }

    return array_values($base);
}

    private static function formatearMercados(array $resumen): array
    {
        $base = [
            'NACIONAL' => [
                'nombre' => 'NACIONAL',
                'individuales' => 0,
                'corporativos' => 0,
                'monto' => 0,
                'icono' => 'flag',
            ],
            'EXTRANJERO' => [
                'nombre' => 'EXTRANJERO',
                'individuales' => 0,
                'corporativos' => 0,
                'monto' => 0,
                'icono' => 'globe',
            ],
        ];

        foreach ($resumen as $r) {
            $texto = self::txt($r, [
                'tipo_mercado',
                'mercado',
                'nombre',
                'tipo',
                'descripcion',
            ]);

            if (str_contains($texto, 'EXTRANJERO')) {
                $key = 'EXTRANJERO';
            } elseif (str_contains($texto, 'NACIONAL')) {
                $key = 'NACIONAL';
            } else {
                continue;
            }

            $individuales = (int)self::num($r, [
                'individuales',
                'individual',
                'total_individuales',
                'total_individual',
                'cantidad_individuales',
                'cantidad_individual',
                'pacs_individuales',
            ]);

            $corporativos = (int)self::num($r, [
                'corporativos',
                'corporativo',
                'total_corporativos',
                'total_corporativo',
                'cantidad_corporativos',
                'cantidad_corporativo',
                'pacs_corporativos',
            ]);

            $base[$key]['individuales'] = $individuales;
            $base[$key]['corporativos'] = $corporativos;
            $base[$key]['monto'] = self::num($r, [
                'monto',
                'estimado',
                'total_estimado',
                'monto_total',
                'total_monto',
                'suma_estimado',
                'valor',
            ]);
        }

        $totalIndividuales = $base['NACIONAL']['individuales'] + $base['EXTRANJERO']['individuales'];
        $totalCorporativos = $base['NACIONAL']['corporativos'] + $base['EXTRANJERO']['corporativos'];
        $totalMonto = $base['NACIONAL']['monto'] + $base['EXTRANJERO']['monto'];

        $salida = array_values($base);

        $salida[] = [
            'nombre' => 'TOTAL',
            'individuales' => $totalIndividuales,
            'corporativos' => $totalCorporativos,
            'monto' => $totalMonto,
            'icono' => 'flag',
        ];

        return $salida;
    }

    private static function formatearObac(array $resumen): array
    {
        $colors = [
            'CONIDA' => '#1f8de2',
            'EP'     => '#f7af42',
            'FAP'    => '#10aaa8',
            'MGP'    => '#0f766e',
            'CCFFAA' => '#6472b4',
        ];

        $base = [];

        foreach ($colors as $obac => $color) {
            $base[$obac] = [
                'nombre' => $obac,
                'valor'  => 0,
                'color'  => $color,
            ];
        }

        foreach ($resumen as $r) {
            $obac = self::txt($r, [
                'obac',
                'nombre',
                'codigo',
                'sigla',
            ]);

            if (!isset($base[$obac])) {
                continue;
            }

            $base[$obac]['valor'] = (int)self::num($r, [
                'total',
                'cantidad',
                'valor',
                'total_pac',
                'total_pacs',
                'pacs',
            ]);
        }

        return array_values($base);
    }
}
