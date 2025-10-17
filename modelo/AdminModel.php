<?php
require_once __DIR__ . '/../bd/conexion.php';

class ModelAdmin {
    private $conexion;

    public function __construct() {
        $this->conexion = new ConexionSupabase();
    }

    public function obtenerUsuarios() {
        return $this->conexion->request("GET", "/usuarios?select=*");
    }

    public function obtenerUsuarioPorId($id_usuario) {
        $endpoint = "/usuarios?id_usuario=eq.$id_usuario&select=*";
        $resultado = $this->conexion->request("GET", $endpoint);
        return $resultado ? $resultado[0] : null;
    }

    public function crearUsuario($data) {
        // Encriptamos contraseña antes de enviarla
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $this->conexion->request("POST", "/usuarios", [$data]);
    }

    public function actualizarUsuario($id_usuario, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        return $this->conexion->request("PATCH", "/usuarios?id_usuario=eq.$id_usuario", $data);
    }

    public function eliminarUsuario($id_usuario) {
        return $this->conexion->request("DELETE", "/usuarios?id_usuario=eq.$id_usuario");
    }
}
?>
