<?php
require_once __DIR__ . "../helpers/Conexion.php";

class Usuario {

    private $con;

    public function __construct() {
        $db = new Conexion();
        $this->con = $db->conectar();
    }

    // Registrar usuario nuevo
    public function registrar($nombre, $direccion, $telefono, $correo, $password, $rol = "cliente") {

        // Verificar si el correo existe
        $sql = $this->con->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $sql->execute([$correo]);

        if ($sql->rowCount() > 0) {
            return "correo_existente";
        }

        // Encriptar contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $query = $this->con->prepare(
            "INSERT INTO usuarios (nombre, direccion, telefono, correo, contraseña, rol)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        return $query->execute([$nombre, $direccion, $telefono, $correo, $password_hash, $rol]);
    }

    // Inicio de sesión
    public function login($correo, $password) {
        $sql = $this->con->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $sql->execute([$correo]);
        $user = $sql->fetch();

        if (!$user) {
            return "no_existente";
        }

        if (!password_verify($password, $user['contraseña'])) {
            return "password_incorrecto";
        }

        return $user; // Devuelve datos completos del usuario
    }

    // Obtener usuario por ID
    public function obtenerUsuario($id) {
        $sql = $this->con->prepare("SELECT * FROM usuarios WHERE id = ?");
        $sql->execute([$id]);
        return $sql->fetch();
    }
}
?>
