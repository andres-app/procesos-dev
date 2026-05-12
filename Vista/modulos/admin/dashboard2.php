<?php
// Vista/modulos/admin/dashboard2.php
$titulo = $titulo ?? 'Dashboard 2';
$active = $active ?? 'dashboard2';

$dashboardData = $dashboardData ?? [
    'anio' => 2026,
    'listas' => [],
    'mercados' => [],
    'obac' => [],
];

require __DIR__ . '/../../layout/admin_layout.php';
?>

<main class="dashboard d2-dashboard">

    <section class="top-row" id="topRow"></section>

    <section class="content-row">

        <div>
            <article class="panel market-panel">
                <h2 class="panel-title">INFORME POR TIPO DE MERCADO</h2>
                <div class="market-grid" id="marketGrid"></div>
            </article>

            <div class="summary-table-wrap">
                <table class="summary-table">
                    <tbody id="summaryTable"></tbody>
                </table>
            </div>
        </div>

        <article class="panel obac-panel">
            <h2 class="panel-title">INFORME POR OBAC</h2>

            <div class="obac-body">
                <div class="legend" id="legendObac"></div>

                <div class="pie-box">
                    <div class="pie" id="pieObac"></div>
                </div>
            </div>
        </article>

    </section>

</main>

<style>
    .d2-dashboard,
    .d2-dashboard * {
        box-sizing: border-box;
    }

    .d2-dashboard {
        width: 100%;
        max-width: 1220px;
        margin: 0 auto;
        color: #0f172a;
        font-family: inherit;
    }

    .d2-dashboard .top-row {
        display: grid;
        grid-template-columns: 150px repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .d2-dashboard .year-card {
        background: #9b1024;
        color: #fff;
        border-radius: 18px;
        min-height: 126px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 16px 36px rgba(122, 12, 25, .18);
    }

    .d2-dashboard .year-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        line-height: 1.05;
        text-align: center;
    }

    .d2-dashboard .year-subtitle {
        font-size: 11px;
        font-weight: 600;
        margin-top: 4px;
    }

    .d2-dashboard .year-number {
        font-size: 38px;
        line-height: .95;
        font-weight: 700;
        margin-top: 5px;
    }

    .d2-dashboard .year-link {
        margin-top: 8px;
        color: rgba(255, 255, 255, .9);
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
    }

    .d2-dashboard .stat-card {
        min-height: 126px;
        padding: 18px 14px;
        text-align: center;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, .22);
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        box-shadow: 0 14px 34px rgba(15, 23, 42, .06);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .d2-dashboard .stat-title {
        font-size: 14px;
        line-height: 1;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
    }

    .d2-dashboard .stat-desc {
        margin-top: 7px;
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
    }

    .d2-dashboard .stat-number {
        margin-top: 9px;
        font-size: 38px;
        line-height: .9;
        font-weight: 700;
        color: #0f172a;
    }

    .d2-dashboard .stat-line {
        margin-top: 10px;
        font-size: 12px;
        line-height: 1.15;
        color: #059669;
        font-weight: 600;
        white-space: nowrap;
    }

    .d2-dashboard .stat-line strong {
        font-weight: 700;
    }

    .d2-dashboard .stat-line-2 {
        margin-top: 3px;
        font-size: 11px;
        color: #059669;
        font-weight: 600;
    }

    .d2-dashboard .total-card .stat-number,
    .d2-dashboard .total-card .stat-line,
    .d2-dashboard .total-card .stat-line-2 {
        color: #059669;
    }

    .d2-dashboard .total-card .stat-line {
        margin-top: 11px;
        font-size: 14px;
        font-weight: 700;
        font-style: italic;
    }

    .d2-dashboard .content-row {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(360px, 1fr);
        gap: 18px;
        align-items: start;
    }

    .d2-dashboard .panel {
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        border: 1px solid rgba(148, 163, 184, .22);
        border-radius: 22px;
        box-shadow: 0 16px 38px rgba(15, 23, 42, .07);
    }

    .d2-dashboard .market-panel {
        padding: 18px 18px 16px;
    }

    .d2-dashboard .obac-panel {
        min-height: 356px;
        padding: 18px;
    }

    .d2-dashboard .panel-title {
        font-size: 14px;
        color: #7A0C19;
        font-weight: 700;
        text-transform: uppercase;
        padding-bottom: 14px;
        border-bottom: 1px solid #dbe3ea;
        margin-bottom: 16px;
    }

    .d2-dashboard .market-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .d2-dashboard .market-card {
        min-height: 92px;
        border: 1px solid #dbe3ea;
        border-radius: 16px;
        display: grid;
        grid-template-columns: 52px 1fr 48px;
        align-items: center;
        padding: 14px;
        background: #f8fafc;
    }

    .d2-dashboard .market-icon {
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .d2-dashboard .market-icon svg {
        width: 36px;
        height: 36px;
        display: block;
        fill: currentColor;
    }

    .d2-dashboard .market-info {
        min-width: 0;
        padding-left: 4px;
    }

    .d2-dashboard .market-title {
        font-size: 14px;
        line-height: 1.1;
        color: #7A0C19;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .d2-dashboard .market-detail {
        margin-top: 7px;
        font-size: 12px;
        line-height: 1.15;
        color: #059669;
        font-weight: 600;
        white-space: nowrap;
    }

    .d2-dashboard .market-detail strong {
        font-size: 13px;
        font-weight: 700;
    }

    .d2-dashboard .market-amount {
        margin-top: 8px;
        font-size: 12px;
        color: #059669;
        font-weight: 700;
        white-space: nowrap;
    }

    .d2-dashboard .market-number {
        justify-self: end;
        align-self: center;
        color: #0f172a;
        font-size: 32px;
        line-height: 1;
        font-weight: 700;
    }

    .d2-dashboard .summary-table-wrap {
        width: 100%;
        margin-top: 16px;
    }

    .d2-dashboard .summary-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        font-family: inherit;
        font-weight: 600;
        overflow: hidden;
        border: 1px solid #dbe3ea;
        border-radius: 16px;
        background: #fff;
    }

    .d2-dashboard .summary-table td {
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        height: 36px;
        padding: 8px 14px;
    }

    .d2-dashboard .summary-table tr:last-child td {
        border-bottom: 0;
    }

    .d2-dashboard .summary-table td:last-child {
        width: 140px;
        text-align: center;
        font-weight: 700;
    }

    .d2-dashboard .summary-table .total td {
        font-weight: 700;
        background: #fff1f2;
        color: #7A0C19;
    }

    .d2-dashboard .obac-body {
        display: grid;
        grid-template-columns: 90px 1fr;
        align-items: center;
        min-height: 260px;
        padding-top: 10px;
    }

    .d2-dashboard .legend {
        display: flex;
        flex-direction: column;
        gap: 10px;
        justify-content: center;
        align-items: flex-start;
    }

    .d2-dashboard .legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        color: #475569;
        font-weight: 600;
        line-height: 1;
    }

    .d2-dashboard .legend-color {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex: 0 0 10px;
        display: inline-block;
    }

    .d2-dashboard .pie-box {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .d2-dashboard .pie {
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: #e5e5e5;
        box-shadow: 0 18px 34px rgba(15, 23, 42, .12);
    }

    @media (max-width: 1180px) {
        .d2-dashboard .top-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .d2-dashboard .content-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {

        .d2-dashboard .top-row,
        .d2-dashboard .market-grid {
            grid-template-columns: 1fr;
        }

        .d2-dashboard .obac-body {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .d2-dashboard .legend {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .d2-dashboard .pie {
            width: 190px;
            height: 190px;
        }
    }
</style>

<script>
    const dashboardData = <?= json_encode(
                                $dashboardData,
                                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK
                            ); ?>;

    function formatoSoles(valor) {
        return "S/. " + Number(valor || 0).toLocaleString("es-PE", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function iconoMercado(tipo) {
        if (tipo === "globe") {
            return `
                <svg viewBox="0 0 64 64" aria-hidden="true">
                    <circle cx="32" cy="32" r="27"></circle>
                    <path d="M22 10c-3 4-5 8-6 13l8 2 4-5-1-7z" fill="#fff" opacity=".9"></path>
                    <path d="M37 8l-4 8 5 6 8 1 2-7c-3-4-7-7-11-8z" fill="#fff" opacity=".9"></path>
                    <path d="M15 31l8-2 6 5-1 8-8 2c-3-3-5-8-5-13z" fill="#fff" opacity=".9"></path>
                    <path d="M39 34l9 2 1 9c-4 5-9 8-15 9l-3-7 4-6z" fill="#fff" opacity=".9"></path>
                    <path d="M28 22l7 1 4 7-5 6-8-2-2-7z" fill="#fff" opacity=".9"></path>
                </svg>
            `;
        }

        return `
            <svg viewBox="0 0 64 64" aria-hidden="true">
                <rect x="10" y="10" width="4" height="45" rx="1"></rect>
                <circle cx="12" cy="9" r="4"></circle>
                <path d="M18 13c8-5 16 4 26 0 3-1 6-3 10-4v27c-4 1-7 3-10 4-10 4-18-5-26 0V13z"></path>
            </svg>
        `;
    }

    function renderDashboard(data) {
        data = data || {};
        data.listas = Array.isArray(data.listas) ? data.listas : [];
        data.mercados = Array.isArray(data.mercados) ? data.mercados : [];
        data.obac = Array.isArray(data.obac) ? data.obac : [];

        renderTopRow(data);
        renderMercados(data.mercados);
        renderTablaObac(data.obac);
        renderLeyendaObac(data.obac);
        renderPieObac(data.obac);
    }

    function renderTopRow(data) {
        const topRow = document.getElementById("topRow");
        const listas = data.listas || [];
        const mercados = data.mercados || [];

        const totalPac = Number(data.total_pacs || 0);
        const totalMonto = mercados.reduce((sum, item) => {
            if ((item.nombre || '').toUpperCase() === 'TOTAL') {
                return sum;
            }

            return sum + Number(item.monto || 0);
        }, 0);

        let html = `
            <article class="year-card">
                <div class="year-title">PLAN ANUAL DE CONTRAT.</div>
                <div class="year-subtitle">Año Fiscal</div>
                <div class="year-number">${data.anio || 2026}</div>
                <a href="#" class="year-link">▣ Seleccionar año</a>
            </article>
        `;

        listas.forEach(item => {
            const total = Number(item.total || 0);
            const individuales = Number(item.individuales || 0);
            const corporativos = Number(item.corporativos || 0);
            const porcentaje = totalPac > 0 ? ((total / totalPac) * 100).toFixed(0) : 0;

            html += `
                <article class="stat-card">
                    <div class="stat-title">${item.codigo || '-'}</div>
                    <div class="stat-desc">${item.descripcion || ''}</div>
                    <div class="stat-number">${total}</div>
                    <div class="stat-line">
                        <strong>${individuales}</strong> Individuales
                        <strong>${corporativos}</strong> Corporativos
                    </div>
                    <div class="stat-line-2">
                        <strong>${porcentaje}</strong> % del total de PAC's
                    </div>
                </article>
            `;
        });

        html += `
            <article class="stat-card total-card">
                <div class="stat-title">En Total</div>
                <div class="stat-desc">Listas Generales</div>
                <div class="stat-number">${totalPac}</div>
                <div class="stat-line">${formatoSoles(totalMonto)}</div>
            </article>
        `;

        topRow.innerHTML = html;
    }

    function renderMercados(mercados) {
        const marketGrid = document.getElementById("marketGrid");
        marketGrid.innerHTML = "";

        if (!mercados.length) {
            marketGrid.innerHTML = `
                <div class="market-card">
                    <div class="market-info">
                        <div class="market-title">SIN REGISTROS</div>
                        <div class="market-detail">No hay información disponible</div>
                    </div>
                    <div class="market-number">0</div>
                </div>
            `;
            return;
        }

        mercados.forEach(item => {
            const individuales = Number(item.individuales || 0);
            const corporativos = Number(item.corporativos || 0);
            const total = individuales + corporativos;

            marketGrid.innerHTML += `
                <div class="market-card">
                    <div class="market-icon">
                        ${iconoMercado(item.icono || 'flag')}
                    </div>

                    <div class="market-info">
                        <div class="market-title">${item.nombre || 'SIN MERCADO'}</div>
                        <div class="market-detail">
                            <strong>${individuales}</strong> Individuales&nbsp;&nbsp;
                            <strong>${corporativos}</strong> Corporativos
                        </div>
                        <div class="market-amount">${formatoSoles(item.monto || 0)}</div>
                    </div>

                    <div class="market-number">${total}</div>
                </div>
            `;
        });
    }

    function renderTablaObac(obac) {
        const tbody = document.getElementById("summaryTable");
        const total = obac.reduce((sum, item) => sum + Number(item.valor || 0), 0);

        tbody.innerHTML = "";

        if (!obac.length) {
            tbody.innerHTML = `
                <tr>
                    <td>SIN REGISTROS</td>
                    <td>0</td>
                </tr>
                <tr class="total">
                    <td>TOTAL</td>
                    <td>0</td>
                </tr>
            `;
            return;
        }

        obac.forEach(item => {
            tbody.innerHTML += `
                <tr>
                    <td>${item.nombre || '-'}</td>
                    <td>${Number(item.valor || 0)}</td>
                </tr>
            `;
        });

        tbody.innerHTML += `
            <tr class="total">
                <td>TOTAL</td>
                <td>${total}</td>
            </tr>
        `;
    }

    function renderLeyendaObac(obac) {
        const legend = document.getElementById("legendObac");
        legend.innerHTML = "";

        obac.forEach(item => {
            legend.innerHTML += `
                <div class="legend-item">
                    <span class="legend-color" style="background:${item.color || '#94a3b8'}"></span>
                    ${item.nombre || '-'}
                </div>
            `;
        });
    }

    function renderPieObac(obac) {
        const pie = document.getElementById("pieObac");
        const total = obac.reduce((sum, item) => sum + Number(item.valor || 0), 0);

        if (!pie || total === 0) {
            pie.style.background = "#e5e5e5";
            return;
        }

        let acumulado = 0;

        const partes = obac.map(item => {
            const valor = Number(item.valor || 0);
            const inicio = acumulado;
            const grados = (valor / total) * 360;
            acumulado += grados;

            return `${item.color || '#94a3b8'} ${inicio.toFixed(2)}deg ${acumulado.toFixed(2)}deg`;
        });

        pie.style.background = `conic-gradient(from -90deg, ${partes.join(", ")})`;
    }

    renderDashboard(dashboardData);
</script>