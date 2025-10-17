<?php
require_once __DIR__ . '/../modelo/AdminModel.php';

$model = new ModelAdmin();

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'listar':
        echo json_encode($model->obtenerUsuarios());
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($model->crearUsuario($data));
        break;

    case 'actualizar':
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($model->actualizarUsuario($id, $data));
        break;

    case 'eliminar':
        $id = $_GET['id'] ?? null;
        echo json_encode($model->eliminarUsuario($id));
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
        break;
}
?>
