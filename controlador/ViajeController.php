<?php
require_once __DIR__ . "/../modelo/ViajeModel.php";

$model = new ViajeModel();

// Traer datos para los select
$choferes = $model->getChoferesActivos();
$unidades = $model->getUnidadesDisponibles();
$metodosCobro = $model->getMetodosCobro();
$metodosPago = $model->getMetodosPago();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Construir los datos del viaje con defaults
    $data = [
        "id_chofer" => $_POST['id_chofer'],
        "id_unidad" => $_POST['id_unidad'],
        "lugar_inicio" => $_POST['lugar_inicio'],
        "lugar_destino" => $_POST['lugar_destino'],
        "fecha_inicio" => $_POST['fecha_inicio'],
        "fecha_fin" => $_POST['fecha_fin'] ?? null,                 // default null
        "kilometros_recorridos" => $_POST['kilometros_recorridos'] ?? 0, // default 0
        "combustible_usado" => $_POST['combustible_usado'] ?? 0,         // default 0
        "ingreso_total" => $_POST['ingreso_total'],
        "pago_acordado_chofer" => $_POST['pago_acordado_chofer'],
        "metodo_cobro" => $_POST['metodo_cobro'],
        "metodo_pago_chofer" => $_POST['metodo_pago_chofer'] ?? 2,       // default id=2 (transferencia)
        "observaciones" => $_POST['observaciones'] ?? null
    ];

    // Crear viaje
    $res = $model->crearViaje($data);

    if (isset($res['error'])) {
        $mensaje = "Error: " . $res['error'];
    } else {
        $mensaje = "Viaje creado exitosamente";
    }
}


?>
