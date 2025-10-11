<?php
require_once __DIR__ . "/../modelo/ReportesModel.php";

$model = new ReportesModel();

// Traer viajes con total de gastos incluido
$viajes = $model->getViajes();

// Traer datos de filtros
$choferes = $model->getChoferes();
$estados = $model->getEstados();
