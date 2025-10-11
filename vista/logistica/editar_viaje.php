<?php
require_once __DIR__ . "/../../controlador/EditarViajeController.php";

$ctrl = new EditarViajeController();
$ctx = $ctrl->handleRequest();

$mensaje = $ctx['mensaje'] ?? "";
$viaje = $ctx['viaje'] ?? null;
$gastos = $ctx['gastos'] ?? [];
$tiposGasto = $ctx['tipos'] ?? [];
$tiposMap = $ctx['tiposMap'] ?? [];
$estados = $ctx['estados'] ?? [];
$estadosMap = $ctx['estadosMap'] ?? [];
$id = $ctx['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Viaje</title>
    <link rel="stylesheet" href="../diseño/editar_viaje.css">
</head>

<body>

    <!-- Botón para regresar al menú anterior -->
    <p><a href="vista_viajes.php"><button>← Volver al Menú</button></a></p>

    <?php if ($mensaje): ?>
        <p><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <?php if ($viaje): ?>
        <h2>Editar Viaje #<?= htmlspecialchars($viaje['id_viaje'] ?? '') ?></h2>

        <!-- Datos solo lectura -->
        <div>
            <p><strong>Chofer Actual:</strong> <?= htmlspecialchars($viaje['nombre_chofer'] ?? '') ?></p>
            <p><strong>Unidad Actual:</strong> <?= htmlspecialchars($viaje['placa_unidad'] ?? '') ?></p>
            <p><strong>Lugar de Inicio:</strong> <?= htmlspecialchars($viaje['lugar_inicio'] ?? '') ?></p>
            <p><strong>Lugar de Destino:</strong> <?= htmlspecialchars($viaje['lugar_destino'] ?? '') ?></p>
            <p><strong>Fecha Inicio:</strong>
                <?= isset($viaje['fecha_inicio']) ? date('d/m/Y H:i', strtotime($viaje['fecha_inicio'])) : '' ?></p>
            <p><strong>Fecha Fin actual:</strong>
                <?= isset($viaje['fecha_fin']) ? date('d/m/Y H:i', strtotime($viaje['fecha_fin'])) : 'dd/mm/aaaa --:--' ?>
            </p>
            <p><strong>Ingreso Total:</strong> Q <?= number_format($viaje['ingreso_total'] ?? 0, 2) ?></p>
            <p><strong>Pago Acordado al Chofer:</strong> Q <?= number_format($viaje['pago_acordado_chofer'] ?? 0, 2) ?></p>
        </div>

        <hr>

        <!-- Campos editables -->
        <h3>Actualizar Datos del Viaje</h3>
        <form method="POST">
            <label>Kilómetros Recorridos:
                <input type="number" name="kilometros_recorridos"
                    value="<?= htmlspecialchars($viaje['kilometros_recorridos'] ?? 0) ?>">
            </label><br>

            <label>Combustible Usado (G):
                <input type="number" step="0.01" name="combustible_usado"
                    value="<?= htmlspecialchars($viaje['combustible_usado'] ?? 0) ?>">
            </label><br>

            <label>Fecha Fin (editar):
                <input type="datetime-local" name="fecha_fin"
                    value="<?= isset($viaje['fecha_fin']) ? date('Y-m-d\TH:i', strtotime($viaje['fecha_fin'])) : '' ?>">
            </label><br>

            <label>
                <input type="checkbox" name="cobro_realizado" <?= !empty($viaje['cobro_realizado']) ? 'checked' : '' ?>>
                Cobro Realizado
            </label><br>

            <label>
                <input type="checkbox" name="pago_chofer_realizado" <?= !empty($viaje['pago_chofer_realizado']) ? 'checked' : '' ?>>
                Pago a Chofer Realizado
            </label><br>

            <label>Estado:
                <select name="estado">
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e['id_estado'] ?>" <?= ($viaje['estado'] ?? '') == $e['id_estado'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['descripcion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label><br>

            <label>Observaciones:<br>
                <textarea name="observaciones" rows="4"
                    cols="50"><?= htmlspecialchars($viaje['observaciones'] ?? '') ?></textarea>
            </label><br>

            <button type="submit" name="actualizar_viaje">Actualizar Viaje</button>
        </form>

        <hr>

        <!-- Gastos del viaje -->
        <h3>Gastos del Viaje</h3>
        <?php if (!empty($gastos)): ?>
            <table border="1" cellpadding="6">
                <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                </tr>
                <?php foreach ($gastos as $i => $g): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($tiposMap[$g['tipo_gasto']] ?? $g['tipo_gasto']) ?></td>
                        <td><?= htmlspecialchars($g['descripcion'] ?? '') ?></td>
                        <td>Q <?= number_format($g['monto'] ?? 0, 2) ?></td>
                        <td><?= isset($g['creado_en']) ? date('d/m/Y H:i', strtotime($g['creado_en'])) : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay gastos registrados.</p>
        <?php endif; ?>

        <hr>

        <!-- Añadir gastos -->
        <h3>Añadir Gastos</h3>
        <form method="POST">
            <div id="gastos_container">
                <div class="input-moneda">
                    <span>Q</span>
                    <input type="number" step="0.01" name="monto[]" placeholder="Monto" class="moneda">
                    <select name="tipo_gasto[]">
                        <option value="">-- tipo --</option>
                        <?php foreach ($tiposGasto as $t): ?>
                            <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['descripcion']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="descripcion[]" placeholder="Descripción">
                </div>
            </div>
            <button type="button" onclick="agregarGasto()">+ Línea</button>
            <button type="submit" name="agregar_gasto">Guardar Gastos</button>
        </form>

    <?php else: ?>
        <p>No se pudo cargar el viaje.</p>
    <?php endif; ?>

    <script src="../js/editar_viajes.js"></script>
</body>

</html>
