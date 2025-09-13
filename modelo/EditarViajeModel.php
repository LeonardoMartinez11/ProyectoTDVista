<?php
require_once __DIR__ . '/../bd/conexion.php';

class ModelViajes {

    private $conexion;

    public function __construct() {
        $this->conexion = new ConexionSupabase();
    }

    public function obtenerViajePorId($id_viaje) {
        // Traemos el viaje y nombres legibles de chofer y unidad
        $endpoint = "/viajes?id_viaje=eq.$id_viaje&select=*,choferes(id_chofer,nombre),unidades(id_unidad,placa)";
        $resultado = $this->conexion->request("GET", $endpoint);

        if (empty($resultado)) return null;

        $viaje = $resultado[0];

        // Normalizamos nombres legibles
        $viaje['nombre_chofer'] = $viaje['choferes']['nombre'] ?? '';
        $viaje['placa_unidad'] = $viaje['unidades']['placa'] ?? '';

        return $viaje;
    }

    public function obtenerEstados() {
        return $this->conexion->request("GET", "/estadosviaje?select=*");
    }

    public function obtenerTiposGasto() {
        return $this->conexion->request("GET", "/tiposgasto?select=*");
    }

    public function obtenerGastosPorViaje($id_viaje) {
        return $this->conexion->request("GET", "/gastosviaje?id_viaje=eq.$id_viaje&select=*");
    }

    public function actualizarViaje($id_viaje, $data) {
        return $this->conexion->request("PATCH", "/viajes?id_viaje=eq.$id_viaje", $data);
    }

    public function agregarGastos($id_viaje, $gastos) {
        foreach ($gastos as &$g) {
            $g['id_viaje'] = $id_viaje;
        }
        return $this->conexion->request("POST", "/gastosviaje", $gastos);
    }
}
