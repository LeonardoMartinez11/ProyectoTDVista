<?php
require_once __DIR__ . '/../modelo/LoginModel.php';

// Leemos datos JSON enviados desde JS
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    echo json_encode(['success' => false, 'message' => 'Datos no enviados']);
    exit;
}

$nombre_usuario = $datos['usuario'] ?? '';
$password = $datos['contrasena'] ?? '';

$model = new ModelLogin();
$respuesta = $model->validarUsuario($nombre_usuario, $password);

// Retornamos respuesta JSON
echo json_encode($respuesta);
