<?php
require_once __DIR__ . '/../modelo/MetricasViajesModel.php';

class MetricasViajesController {

    private $model;

    public function __construct() {
        $this->model = new MetricasViajesModel();
    }

    public function getDatosViaje($id_viaje) {
        $viaje = $this->model->obtenerViajePorId($id_viaje);
        if (!$viaje) return ['viaje' => null, 'gastosPorTipo' => []];

        $gastos = $this->model->obtenerGastosPorViaje($id_viaje);
        $tipos = $this->model->obtenerTiposGasto();

        $tiposMap = [];
        foreach ($tipos as $t) {
            $tiposMap[$t['id_tipo']] = $t['descripcion'];
        }

        $gastosPorTipo = [];
        foreach ($gastos as $g) {
            $tipo = $tiposMap[$g['tipo_gasto']] ?? 'Otros';
            if (!isset($gastosPorTipo[$tipo])) $gastosPorTipo[$tipo] = 0;
            $gastosPorTipo[$tipo] += floatval($g['monto']);
        }

        // Incluir el pago al chofer como gasto
        $pagoChofer = floatval($viaje['pago_acordado_chofer'] ?? 0);
        if ($pagoChofer > 0) {
            $gastosPorTipo['Pago al Chofer'] = $pagoChofer;
        }

        return [
            'viaje' => $viaje,
            'gastosPorTipo' => $gastosPorTipo
        ];
    }
}
