<?php
require_once __DIR__ . '/../bd/conexion.php';

class MetricasViajesModel {

    private $conexion;

    public function __construct() {
        $this->conexion = new ConexionSupabase();
    }

    public function obtenerViajePorId($id_viaje) {
        // Seleccionamos el campo correcto para el pago al chofer
        $endpoint = "/viajes?id_viaje=eq.$id_viaje&select=*,choferes(id_chofer,nombre),unidades(id_unidad,placa),pago_acordado_chofer";
        $resultado = $this->conexion->request("GET", $endpoint);
        if (empty($resultado)) return null;

        $viaje = $resultado[0];
        $viaje['nombre_chofer'] = $viaje['choferes']['nombre'] ?? '';
        $viaje['placa_unidad'] = $viaje['unidades']['placa'] ?? '';
        $viaje['pago_acordado_chofer'] = floatval($viaje['pago_acordado_chofer'] ?? 0);

        return $viaje;
    }

    public function obtenerGastosPorViaje($id_viaje) {
        return $this->conexion->request("GET", "/gastosviaje?id_viaje=eq.$id_viaje&select=*");
    }

    public function obtenerTiposGasto() {
        return $this->conexion->request("GET", "/tiposgasto?select=*");
    }
}
