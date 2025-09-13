<?php
require_once __DIR__ . '/../modelo/EditarViajeModel.php';

class EditarViajeController {

    private $model;

    public function __construct() {
        $this->model = new ModelViajes();
    }

    public function handleRequest() {
        $ctx = [];
        $ctx['id'] = $_GET['id'] ?? null;

        if (!$ctx['id']) {
            $ctx['mensaje'] = "ID de viaje no especificado.";
            return $ctx;
        }

        // Traemos datos
        $ctx['viaje'] = $this->model->obtenerViajePorId($ctx['id']);
        $ctx['estados'] = $this->model->obtenerEstados();
        $ctx['tipos'] = $this->model->obtenerTiposGasto();
        $ctx['gastos'] = $this->model->obtenerGastosPorViaje($ctx['id']);

        // Map para mostrar tipo de gasto legible
        $ctx['tiposMap'] = [];
        foreach ($ctx['tipos'] as $t) {
            $ctx['tiposMap'][$t['id_tipo']] = $t['descripcion'];
        }

        // Map para estados
        $ctx['estadosMap'] = [];
        foreach ($ctx['estados'] as $e) {
            $ctx['estadosMap'][$e['id_estado']] = $e['descripcion'];
        }

        // POST: Actualizar viaje
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['actualizar_viaje'])) {
                $data = [
                    "kilometros_recorridos" => $_POST['kilometros_recorridos'] ?: null,
                    "combustible_usado" => $_POST['combustible_usado'] ?: null,
                    "fecha_fin" => $_POST['fecha_fin'] ?: null,
                    "cobro_realizado" => isset($_POST['cobro_realizado']),
                    "pago_chofer_realizado" => isset($_POST['pago_chofer_realizado']),
                    "estado" => $_POST['estado'] ?: 1,
                    "observaciones" => $_POST['observaciones'] ?: ''
                ];
                $this->model->actualizarViaje($ctx['id'], $data);
                $ctx['mensaje'] = "Viaje actualizado correctamente.";
                // recargamos viaje
                $ctx['viaje'] = $this->model->obtenerViajePorId($ctx['id']);
            }

            // Agregar gastos
            if (isset($_POST['agregar_gasto'])) {
                $tiposG = $_POST['tipo_gasto'] ?? [];
                $descG = $_POST['descripcion'] ?? [];
                $montosG = $_POST['monto'] ?? [];
                $gastosNuevos = [];

                for ($i = 0; $i < count($tiposG); $i++) {
                    if ($tiposG[$i] && $montosG[$i] != '') {
                        $gastosNuevos[] = [
                            "tipo_gasto" => $tiposG[$i],
                            "descripcion" => $descG[$i],
                            "monto" => $montosG[$i]
                        ];
                    }
                }

                if ($gastosNuevos) {
                    $this->model->agregarGastos($ctx['id'], $gastosNuevos);
                    $ctx['mensaje'] = "Gastos agregados correctamente.";
                    $ctx['gastos'] = $this->model->obtenerGastosPorViaje($ctx['id']);
                }
            }
        }

        return $ctx;
    }
}
