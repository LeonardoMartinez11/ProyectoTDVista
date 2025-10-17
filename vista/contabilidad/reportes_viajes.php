<?php
require_once __DIR__ . "/../../controlador/ReportesController.php";

if (!isset($viajes) || !is_array($viajes)) $viajes = [];

function normalize_estado($v)
{
    $raw = null;
    if (is_array($v) && isset($v['estado'])) {
        $raw = is_array($v['estado']) ? ($v['estado']['descripcion'] ?? null) : $v['estado'];
    } else {
        $raw = $v;
    }
    if ($raw === null) return '';
    $raw = strtolower(trim($raw));
    $map = [
        '1' => 'pendiente', 'pendiente' => 'pendiente',
        '2' => 'en_curso', 'en curso' => 'en_curso',
        '3' => 'finalizado', 'finalizado' => 'finalizado',
        '4' => 'cancelado', 'cancelado' => 'cancelado'
    ];
    return $map[$raw] ?? $raw;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Viajes</title>
    <link rel="stylesheet" href="../diseño/reportes_viajes.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>📊 Reporte de Viajes</h2>
        <a href="vista_viajes.php" class="btn-primary"> <- Volver</a>
    </div>

    <!-- Totales y exportar -->
    <div class="totales-container">
        <div class="total-card">
            <h3>💵 Total Ingresos</h3>
            <p id="total-ingresos">Q0.00</p>
        </div>
        <div class="total-card">
            <h3>💰 Total Pago Chofer</h3>
            <p id="total-pago-chofer">Q0.00</p>
        </div>
        <div class="total-card">
            <h3>💸 Total Otros Gastos</h3>
            <p id="total-gastos">Q0.00</p>
        </div>
        <div class="total-card">
            <h3>📈 Total Rentabilidad</h3>
            <p id="total-rentabilidad">Q0.00</p>
        </div>
        <div class="total-card">
            <button id="btn-exportar" class="btn-export">📤 Exportar a PDF</button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-grid">
        <div class="filter-group">
            <label>Buscar</label>
            <input type="text" id="search" class="filter-input" placeholder="Buscar chofer, destino, unidad...">
        </div>
        <div class="filter-group">
            <label>Chofer</label>
            <select id="chofer" class="filter-input">
                <option value="">Todos</option>
                <?php foreach ($choferes as $c): ?>
                    <option value="<?= htmlspecialchars($c['nombre']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Estado</label>
            <select id="estado" class="filter-input">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_curso">En curso</option>
                <option value="finalizado">Finalizado</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Desde</label>
            <input type="date" id="fecha-desde" class="filter-input">
        </div>
        <div class="filter-group">
            <label>Hasta</label>
            <input type="date" id="fecha-hasta" class="filter-input">
        </div>
        <div class="filter-group">
            <button type="button" id="btn-limpiar" class="btn-filter">Limpiar</button>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="chart-container" style="margin-bottom: 20px;">
        <canvas id="graficoTotales"></canvas>
    </div>

    <!-- Tabla -->
    <div class="table-container">
        <table class="modern-table" id="tabla-reportes">
            <thead>
                <tr>
                    <th>Chofer</th>
                    <th>Unidad</th>
                    <th>Ruta</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Estado</th>
                    <th>Ingresos</th>
                    <th>Pago Chofer</th>
                    <th>Gastos</th>
                    <th>Rentabilidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($viajes as $v): 
                    $estadoNorm = normalize_estado($v);
                    $totalGastos = floatval($v['total_gastos'] ?? 0);
                    $ingreso = floatval($v['ingreso_total'] ?? 0);
                    $pagoChofer = floatval($v['pago_acordado_chofer'] ?? 0);
                    $rentabilidad = $ingreso - $pagoChofer - $totalGastos;
                ?>
                    <tr class="viaje-row"
                        data-estado="<?= $estadoNorm ?>"
                        data-chofer="<?= strtolower($v['chofer']['nombre'] ?? '') ?>"
                        data-fecha="<?= $v['fecha_inicio'] ?? '' ?>"
                        data-ingresos="<?= $ingreso ?>"
                        data-pago="<?= $pagoChofer ?>"
                        data-gastos="<?= $totalGastos ?>"
                        data-rentabilidad="<?= $rentabilidad ?>">
                        <td><?= htmlspecialchars($v['chofer']['nombre'] ?? '') ?></td>
                        <td><?= htmlspecialchars($v['unidad']['placa'] ?? '') ?></td>
                        <td><?= htmlspecialchars($v['lugar_inicio'] ?? '') ?> → <?= htmlspecialchars($v['lugar_destino'] ?? '') ?></td>
                        <td><?= htmlspecialchars($v['fecha_inicio'] ?? '') ?></td>
                        <td><?= htmlspecialchars($v['fecha_fin'] ?? '') ?></td>
                        <td><?= ucfirst(str_replace('_',' ', $estadoNorm)) ?></td>
                        <td>Q<?= number_format($ingreso, 2) ?></td>
                        <td>Q<?= number_format($pagoChofer, 2) ?></td>
                        <td>Q<?= number_format($totalGastos, 2) ?></td>
                        <td>Q<?= number_format($rentabilidad, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
<script src="../js/reportes_viajes.js"></script>
</body>
</html>
