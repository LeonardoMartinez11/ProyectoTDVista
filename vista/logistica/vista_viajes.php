<?php
require_once __DIR__ . "/../../controlador/VistaViajesController.php";

// Asegurarnos de que $viajes exista
if (!isset($viajes) || !is_array($viajes))
    $viajes = [];


function normalize_estado($v)
{
    // $v puede ser el array del viaje o el valor crudo del estado
    $raw = null;

    // si pasaron el viaje completo
    if (is_array($v) && array_key_exists('estado', $v)) {
        $raw = $v['estado'];
        // si viene anidado
        if (is_array($raw)) {
            if (isset($raw['descripcion']))
                $raw = $raw['descripcion'];
            elseif (isset($raw['id_estado']))
                $raw = $raw['id_estado'];
        }
    } else {
        // si pasaron directamente el valor del estado
        $raw = $v;
    }

    if ($raw === null || $raw === '')
        return '';

    // si es numérico -> mapear por id
    if (is_int($raw) || (is_string($raw) && ctype_digit((string) $raw))) {
        $map = [
            1 => 'pendiente',
            2 => 'en_curso',
            3 => 'finalizado',
            4 => 'cancelado'
        ];
        $id = intval($raw);
        return $map[$id] ?? (string) $raw;
    }

    // si es string -> normalizar texto
    $s = (string) $raw;
    $s = trim($s);
    $s = mb_strtolower($s, 'UTF-8');
    // reemplazar espacios/hyphens por underscore
    $s = str_replace([' ', '-', '/'], ['_', '_', '_'], $s);
    // quitar tildes y caracteres especiales
    $s = strtr($s, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
    // mantener sólo letras, numeros y underscore
    $s = preg_replace('/[^a-z0-9_]/', '', $s);

    // si viene "encurso" o "en_curso" queremos "en_curso"
    if ($s === 'encurso')
        $s = 'en_curso';
    if ($s === 'terminado')
        $s = 'finalizado';

    // normalizar guardadas esperadas
    $valid = ['pendiente', 'en_curso', 'finalizado', 'cancelado'];
    return in_array($s, $valid) ? $s : $s;
}

// Etiqueta amigable desde la clave normalizada
function label_estado($key)
{
    $map = [
        'pendiente' => 'Pendiente',
        'en_curso' => 'En curso',
        'finalizado' => 'Finalizado',
        'cancelado' => 'Cancelado'
    ];
    return $map[$key] ?? ucfirst(str_replace('_', ' ', $key));
}

// Calcular contadores y suma de ingresos (solo finalizados)
$counts = [
    'total' => count($viajes),
    'en_curso' => 0,
    'finalizado' => 0,
    'pendiente' => 0,
    'cancelado' => 0
];
$ingresos_mes = 0.0;

foreach ($viajes as $v) {
    $est = normalize_estado($v);
    if (isset($counts[$est]))
        $counts[$est]++;

    // sumar ingresos solo si finalizado
    if ($est === 'finalizado') {
        $ing = $v['ingreso_total'] ?? 0;
        // convertir posibles formatos extraños a número
        if (is_string($ing)) {
            $ing = str_replace(['Q', ',', ' '], ['', '', ''], $ing);
        }
        $ingresos_mes += floatval($ing);
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viajes</title>
    <link rel="stylesheet" href="../diseño/vista_viajes.css">
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h2>🚛 Gestión de Viajes</h2>
                <div class="header-actions">
                    <a href="crear_viajes.php" class="btn-primary">+ Nuevo Viaje</a>
                    <a href="reportes_viajes.php" class="btn-primary">📊 Reportes</a>
                    <a href="../../index.php" class="btn-primary">Menu</a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Viajes</h3>
                <div class="number"><?= $counts['total'] ?></div>
            </div>
            <div class="stat-card">
                <h3>En Progreso</h3>
                <div class="number"><?= $counts['en_curso'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Completados</h3>
                <div class="number"><?= $counts['finalizado'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Ingresos Mes</h3>
                <div class="number">Q<?= number_format($ingresos_mes, 2) ?></div>
            </div>

        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="search">Buscar viajes</label>
                    <input type="text" id="search" class="filter-input" placeholder="Chofer, destino, placa...">
                </div>
                <div class="filter-group">
                    <label for="estado">Estado</label>
                    <select id="estado" class="filter-input">
                        <option value="">Todos los estados</option>
                        <option value="en_curso">En curso</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="pendiente">Pendiente</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" class="filter-input">
                </div>
                <div class="filter-group">
                    <button type="button" class="btn-filter">Limpiar</button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-container">
            <div class="table-wrapper">
                <?php if (empty($viajes)): ?>
                    <div class="empty-state">
                        <div class="icon">🚛</div>
                        <h3>No hay viajes registrados</h3>
                        <p>Comienza creando tu primer viaje</p>
                        <a href="crear_viaje.php" class="btn-primary">+ Crear Viaje</a>
                    </div>
                <?php else: ?>
                    <table class="modern-table" id="viajes-table">
                        <thead>
                            <tr>
                                <th>Chofer</th>
                                <th>Unidad</th>
                                <th>Ruta</th>
                                <th>Fechas</th>
                                <th>Estado</th>
                                <th>Ingresos</th>
                                <th>Pago Chofer</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($viajes as $v):
                                $estadoNorm = normalize_estado($v);
                                $estadoLabel = label_estado($estadoNorm);
                                $estadoClass = 'status-badge ';
                                switch ($estadoNorm) {
                                    case 'en_curso':
                                        $estadoClass .= 'activo';
                                        break;
                                    case 'finalizado':
                                        $estadoClass .= 'completado';
                                        break;
                                    case 'cancelado':
                                        $estadoClass .= 'cancelado';
                                        break;
                                    case 'pendiente':
                                    default:
                                        $estadoClass .= 'pendiente';
                                }
                                ?>
                                <tr class="viaje-row" data-estado="<?= htmlspecialchars($estadoNorm) ?>">
                                    <td>
                                        <div style="font-weight: 600; color: #2d3748;">
                                            <?= htmlspecialchars($v['chofer']['nombre'] ?? 'Chofer ID: ' . ($v['id_chofer'] ?? '')) ?>
                                        </div>
                                        <?php if (!empty($v['chofer']['licencia'])): ?>
                                            <div style="font-size: 0.8rem; color: #718096;">
                                                Lic. <?= htmlspecialchars($v['chofer']['licencia']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="vehicle-info">
                                            <span class="vehicle-plate">
                                                <?= htmlspecialchars($v['unidad']['placa'] ?? 'ID: ' . ($v['id_unidad'] ?? '')) ?>
                                            </span>
                                            <?php if (isset($v['unidad']['capacidad_toneladas'])): ?>
                                                <span class="vehicle-capacity">
                                                    <?= $v['unidad']['capacidad_toneladas'] ?>t
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #2d3748;">
                                            <?= htmlspecialchars($v['lugar_inicio'] ?? '') ?> →
                                            <?= htmlspecialchars($v['lugar_destino'] ?? '') ?>
                                        </div>
                                        <?php if (!empty($v['distancia_km'])): ?>
                                            <div style="font-size: 0.8rem; color: #718096;">
                                                <?= htmlspecialchars($v['distancia_km']) ?> km
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #2d3748;">
                                            <?= isset($v['fecha_inicio']) ? date('Y-m-d', strtotime($v['fecha_inicio'])) : '' ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #718096;">
                                            <?= isset($v['fecha_inicio']) ? date('H:i A', strtotime($v['fecha_inicio'])) : '' ?>
                                        </div>
                                        <?php if (!empty($v['fecha_fin'])): ?>
                                            <div style="font-size: 0.8rem; color: #10b981;">
                                                Fin: <?= date('Y-m-d H:i', strtotime($v['fecha_fin'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="<?= $estadoClass ?>">
                                            <?= htmlspecialchars($estadoLabel) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="money">Q<?= number_format(floatval($v['ingreso_total'] ?? 0), 2) ?></span>
                                    </td>
                                    <td>
                                        <span
                                            class="money">Q<?= number_format(floatval($v['pago_acordado_chofer'] ?? 0), 2) ?></span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="editar_viaje.php?id=<?= htmlspecialchars($v['id_viaje'] ?? '') ?>"
                                                class="btn-action btn-edit">
                                                Editar
                                            </a>
                                            <a href="metricas_viaje.php?id=<?= htmlspecialchars($v['id_viaje'] ?? '') ?>"
                                                class="btn-action btn-metrics">
                                                Métricas
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Paginación (si se llegara a implementar) -->
        <div class="pagination">
            <a href="#">« Anterior</a>
            <span class="current">1</span>
            <a href="#">2</a>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
            <a href="#">Siguiente »</a>
        </div>
    </div>
    <script src="../js/vista_viajes.js"></script>
</body>

</html>