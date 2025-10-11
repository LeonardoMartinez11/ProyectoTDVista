<?php
require_once __DIR__ . '/../../controlador/MetricasViajesController.php';

$id_viaje = $_GET['id'] ?? null;
$ctrl = new MetricasViajesController();
$datos = $ctrl->getDatosViaje($id_viaje);

$viaje = $datos['viaje'];
$gastosPorTipo = $datos['gastosPorTipo'];

$ingresoTotal = floatval($viaje['ingreso_total'] ?? 0);
$totalGastos = array_sum(array_values($gastosPorTipo));
$pagoChofer = floatval($viaje['pago_acordado_chofer'] ?? 0);
$gananciaNeta = $ingresoTotal - $totalGastos - $pagoChofer;
$rentable = $gananciaNeta >= 0 ? "Sí" : "No";
$rentabilidadPorc = $ingresoTotal > 0 ? ($gananciaNeta / $ingresoTotal) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Métricas del Viaje #<?= htmlspecialchars($id_viaje) ?></title>
    <link rel="stylesheet" href="../diseño/metrica_viaje.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
<div class="container">
    <a href="vista_viajes.php" class="btn-primary">← Regresar a Viajes</a>
    <h2>Métricas del Viaje #<?= htmlspecialchars($id_viaje) ?></h2>

    <?php if ($viaje): ?>
        <p><strong>Chofer:</strong> <?= htmlspecialchars($viaje['nombre_chofer'] ?? '') ?></p>
        <p><strong>Unidad:</strong> <?= htmlspecialchars($viaje['placa_unidad'] ?? '') ?></p>
        <p><strong>Origen:</strong> <?= htmlspecialchars($viaje['lugar_inicio'] ?? '') ?></p>
        <p><strong>Destino:</strong> <?= htmlspecialchars($viaje['lugar_destino'] ?? '') ?></p>
        <p><strong>Kilómetros Recorridos:</strong> <?= htmlspecialchars($viaje['kilometros_recorridos'] ?? 0) ?></p>
        <p><strong>Combustible Usado:</strong> <?= htmlspecialchars($viaje['combustible_usado'] ?? 0) ?> G</p>
        <p><strong>Ingreso Total:</strong> Q<?= number_format($ingresoTotal, 2) ?></p>
        <p><strong>Pago al Chofer:</strong> Q<?= number_format($pagoChofer, 2) ?></p>
        <p><strong>Gastos Totales:</strong> Q<?= number_format($totalGastos, 2) ?></p>
        <p><strong>Ganancia Neta:</strong> Q<?= number_format($gananciaNeta, 2) ?></p>
        <p><strong>Rentable:</strong> <?= $rentable ?></p>
        <p><strong>Rentabilidad:</strong> <?= number_format($rentabilidadPorc, 2) ?>%</p>

        <!-- Botón de exportación arriba -->
        <button id="exportPDF" class="btn-primary" style="margin-bottom:20px;">Exportar a PDF</button>

        <h3>Gastos por Tipo (Q)</h3>
        <div class="chart-wrapper">
            <canvas id="gastosChart"></canvas>
        </div>

        <h3>Comparativa Ingresos vs Gastos vs Ganancia Neta</h3>
        <div class="chart-wrapper">
            <canvas id="comparativaChart"></canvas>
        </div>

        <!-- Tabla de gastos -->
        <?php if (!empty($gastosPorTipo)): ?>
            <h3>Detalle de Gastos por Tipo</h3>
            <table class="tabla-gastos">
                <thead>
                    <tr>
                        <th>Tipo de Gasto</th>
                        <th>Monto (Q)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gastosPorTipo as $tipo => $monto): ?>
                        <tr>
                            <td><?= htmlspecialchars($tipo) ?></td>
                            <td><?= number_format($monto, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th><?= number_format($totalGastos, 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    <?php else: ?>
        <p>No se encontró información del viaje.</p>
    <?php endif; ?>
</div>

<script>
    // Variables para JS
    const gastoLabels = <?= json_encode(array_keys($gastosPorTipo)) ?>;
    const gastoData = <?= json_encode(array_values($gastosPorTipo)) ?>;

    const idViaje = <?= json_encode($id_viaje) ?>;
    const nombreChofer = <?= json_encode($viaje['nombre_chofer']) ?>;
    const placaUnidad = <?= json_encode($viaje['placa_unidad']) ?>;
    const origen = <?= json_encode($viaje['lugar_inicio']) ?>;
    const destino = <?= json_encode($viaje['lugar_destino']) ?>;
    const kilometros = <?= json_encode($viaje['kilometros_recorridos']) ?>;
    const combustible = <?= json_encode($viaje['combustible_usado']) ?>;

    const ingresoTotal = <?= $ingresoTotal ?>;
    const pagoChofer = <?= $pagoChofer ?>;
    const totalGastos = <?= $totalGastos ?>;
    const gananciaNeta = <?= $gananciaNeta ?>;
    const rentabilidadPorc = <?= $rentabilidadPorc ?>;
    const rentable = <?= json_encode($rentable) ?>;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="../js/metricas_viajes.js"></script>
</body>
</html>
