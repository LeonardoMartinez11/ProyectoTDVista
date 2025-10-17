<?php
require_once __DIR__ . '/../bd/conexion.php';

class ModelLogin {
    private $conexion;

    public function __construct() {
        $this->conexion = new ConexionSupabase();
    }

    // Validar usuario y contraseña
    public function validarUsuario($nombre_usuario, $password) {
        // Traemos datos del usuario con su rol
        $endpoint = "/usuarios?nombre_usuario=eq.$nombre_usuario&select=*,roles(nombre_rol)";
        $resultado = $this->conexion->request("GET", $endpoint);

        if (empty($resultado)) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        $usuario = $resultado[0];

        // Verificamos que esté activo
        if (!$usuario['activo']) {
            return ['success' => false, 'message' => 'Usuario inactivo'];
        }

        // Verificamos la contraseña
        if (!password_verify($password, $usuario['password_hash'])) {
            return ['success' => false, 'message' => 'Contraseña incorrecta'];
        }

        return [
            'success' => true,
            'usuario' => $usuario['nombre_usuario'],
            'nombre_completo' => $usuario['nombre_completo'],
            'rol' => $usuario['roles']['nombre_rol']
        ];
    }
}
