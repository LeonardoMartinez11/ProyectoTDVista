<?php
require_once __DIR__ . "/../bd/Conexion.php";

class ViajeModel {
    private $conexion;

    public function __construct() {
        $this->conexion = new ConexionSupabase();
    }

    /* ======================
       CRUD VIAJES
    ====================== */
    public function crearViaje($data) {
        return $this->conexion->request("POST", "/viajes", $data);
    }

    /* ======================
       OBTENER CHOFERES Y UNIDADES PARA SELECT
    ====================== */
    public function getChoferesActivos() {
        return $this->conexion->request("GET", "/choferes?activo=eq.true");
    }

    public function getUnidadesDisponibles() {
        return $this->conexion->request("GET", "/unidades?estado=eq.1"); // 1 = disponible
    }

    public function getMetodosCobro() {
        return $this->conexion->request("GET", "/metodoscobro");
    }

    public function getMetodosPago() {
        return $this->conexion->request("GET", "/metodospago");
    }


}
?>
