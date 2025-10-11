<?php
require_once __DIR__ . "/../bd/conexion.php";

class ReportesModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new ConexionSupabase();
    }

    public function getViajes()
    {
        // Endpoint Supabase corregido con la relación correcta
        $endpoint = "/viajes?select=*,chofer:id_chofer(nombre),unidad:id_unidad(placa),estado:estado(descripcion),gastosviaje(monto)&order=fecha_inicio.desc";
        $viajes = $this->conexion->request("GET", $endpoint);

        // Validar que la respuesta sea un array
        if (!is_array($viajes)) {
            return [];
        }

        // Calcular total de gastos por viaje
        foreach ($viajes as &$viaje) {
            $viaje['total_gastos'] = 0;
            if (isset($viaje['gastosviaje']) && is_array($viaje['gastosviaje'])) {
                foreach ($viaje['gastosviaje'] as $gasto) {
                    $viaje['total_gastos'] += floatval($gasto['monto']);
                }
            }
        }

        return $viajes;
    }

    public function getChoferes()
    {
        return $this->conexion->request("GET", "/choferes?select=id_chofer,nombre");
    }

    public function getEstados()
    {
        return [
            ["id" => 1, "descripcion" => "Pendiente"],
            ["id" => 2, "descripcion" => "En curso"],
            ["id" => 3, "descripcion" => "Finalizado"],
            ["id" => 4, "descripcion" => "Cancelado"]
        ];
    }
}
