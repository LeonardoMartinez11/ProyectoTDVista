<?php
require_once __DIR__ . "/../modelo/VistaViajesModel.php";

$model = new ViajeModel();
$viajes = $model->getViajes();
?>
