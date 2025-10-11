<?php
require_once __DIR__ . "/../../controlador/ViajeController.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Viaje</title>
    <link rel="stylesheet" href="../diseño/crear_viaje.css">
</head>

<body>
    <a href="vista_viajes.php" class="btn-primary">⬅ Volver</a>
    <div class="container">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Crear Nuevo Viaje</h2>
            
        </div>

        <div class="form-container">
            <?php if ($mensaje): ?>
                <div class="mensaje">
                    <p><?= htmlspecialchars($mensaje) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" id="form-crear-viaje">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="id_chofer">Chofer:</label>
                        <select name="id_chofer" id="id_chofer" required>
                            <option value="">--Seleccione--</option>
                            <?php foreach ($choferes as $c): ?>
                                <option value="<?= $c['id_chofer'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_unidad">Unidad:</label>
                        <select name="id_unidad" id="id_unidad" required>
                            <option value="">--Seleccione--</option>
                            <?php foreach ($unidades as $u): ?>
                                <option value="<?= $u['id_unidad'] ?>">
                                    <?= htmlspecialchars($u['placa']) ?> (<?= $u['capacidad_toneladas'] ?> t)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lugar_inicio">Lugar de Inicio:</label>
                        <input type="text" name="lugar_inicio" id="lugar_inicio" required>
                    </div>

                    <div class="form-group">
                        <label for="lugar_destino">Lugar de Destino:</label>
                        <input type="text" name="lugar_destino" id="lugar_destino" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio:</label>
                        <input type="datetime-local" name="fecha_inicio" id="fecha_inicio" required>
                    </div>

                    <div class="form-group">
                        <label for="metodo_cobro">Método de Cobro:</label>
                        <select name="metodo_cobro" id="metodo_cobro">
                            <?php foreach ($metodosCobro as $m): ?>
                                <option value="<?= $m['id_metodo'] ?>"><?= htmlspecialchars($m['descripcion']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ingreso_total">Ingreso Total:</label>
                        <div style="display: flex; align-items: center;">
                            <span style="margin-right: 5px;">Q</span>
                            <input type="number" step="0.01" name="ingreso_total" id="ingreso_total" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pago_acordado_chofer">Pago Acordado a Chofer:</label>
                        <div style="display: flex; align-items: center;">
                            <span style="margin-right: 5px;">Q</span>
                            <input type="number" step="0.01" name="pago_acordado_chofer" id="pago_acordado_chofer"
                                required>
                        </div>
                    </div>


                    <div class="form-group full-width">
                        <label for="observaciones">Observaciones:</label>
                        <textarea name="observaciones" id="observaciones"
                            placeholder="Ingrese observaciones adicionales (opcional)"></textarea>
                    </div>
                </div>

                <div class="submit-container">
                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Crear Viaje</span>
                        <span class="btn-loading" style="display: none;">Creando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/crear_viajes.js"></script>
</body>

</html>