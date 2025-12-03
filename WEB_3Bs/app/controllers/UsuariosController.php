<?php
require_once __DIR__ . "/../helpers/Conexion.php";


class UsuariosController {

    private $conn;

    public function __construct()
    {
        $db = new Conexion();
        $conexion = $db->conectar();
        $this->conn = $conexion;
    }

    // LISTAR TODOS LOS USUARIOS
    public function getAll()
    {
        $sql = "SELECT * FROM usuarios";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // OBTENER UN USUARIO POR ID
    public function getById($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // INSERTAR USUARIO
    public function insert($data)
    {
        $sql = "INSERT INTO usuarios (nombre, direccion, telefono, correo, contraseña, rol)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        // Hashear contraseña
        $passHash = password_hash($data['contraseña'], PASSWORD_BCRYPT);

        return $stmt->execute([
            $data['nombre'],
            $data['direccion'],
            $data['telefono'],
            $data['correo'],
            $passHash,
            $data['rol']
        ]);
    }

    // ACTUALIZAR USUARIO
    public function update($id, $data)
    {
        if (!empty($data['contraseña'])) {
            $passHash = password_hash($data['contraseña'], PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET nombre=?, direccion=?, telefono=?, correo=?, contraseña=?, rol=? WHERE id=?";
            $params = [
                $data['nombre'], $data['direccion'], $data['telefono'],
                $data['correo'], $passHash, $data['rol'], $id
            ];
        } else {
            $sql = "UPDATE usuarios SET nombre=?, direccion=?, telefono=?, correo=?, rol=? WHERE id=?";
            $params = [
                $data['nombre'], $data['direccion'], $data['telefono'],
                $data['correo'], $data['rol'], $id
            ];
        }

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    // ELIMINAR USUARIO
    public function delete($id)
    {
        $sql = "DELETE FROM usuarios WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}
