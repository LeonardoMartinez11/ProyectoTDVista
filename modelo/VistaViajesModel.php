<?php
require_once __DIR__ . "/../bd/Conexion.php";

class ViajeModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new ConexionSupabase();
    }

    public function getViajes()
    {
        // Traer todos los viajes, con chofer, unidad y estado
        $endpoint = "/viajes?select=*,chofer:id_chofer(nombre),unidad:id_unidad(placa,capacidad_toneladas),estado:estado(descripcion)&order=fecha_inicio.desc";
        return $this->conexion->request("GET", $endpoint);
    }

    public function getChoferesActivos()
    {
        return $this->conexion->request("GET", "/choferes?activo=eq.true");
    }

    public function getUnidadesDisponibles()
    {
        return $this->conexion->request("GET", "/unidades?estado=eq.1"); // estado 1 = disponible
    }

    public function getMetodosCobro()
    {
        return $this->conexion->request("GET", "/metodoscobro");
    }

    public function getMetodosPago()
    {
        return $this->conexion->request("GET", "/metodospago");
    }

}

