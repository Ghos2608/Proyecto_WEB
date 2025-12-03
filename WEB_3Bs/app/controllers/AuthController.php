<?php
session_start();
require_once "../helpers/Conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $db = new Conexion();
    $conexion = $db->conectar();

    $action = $_POST['action'] ?? '';

    /* =====================================================
       =============== REGISTRO DE USUARIO =================
       ===================================================== */
    if ($action === "register") {

        try {
            if (
                empty($_POST['nombre']) || empty($_POST['correo']) ||
                empty($_POST['telefono']) || empty($_POST['direccion']) ||
                empty($_POST['password'])
            ) {
                die("Todos los campos son obligatorios.");
            }

            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, direccion, telefono, correo, contraseña, rol) 
                    VALUES (:nombre, :direccion, :telefono, :correo, :password, 'Cliente')";
            
            $stmt = $conexion->prepare($sql);

            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":direccion", $direccion);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":correo", $correo);
            $stmt->bindParam(":password", $password);

            if ($stmt->execute()) {
                $_SESSION['usuario_id'] = $conexion->lastInsertId();
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_correo'] = $correo;
                $_SESSION['usuario_telefono'] = $telefono;
                $_SESSION['usuario_direccion'] = $direccion;
                $_SESSION['usuario_rol'] = "Cliente";

                header("Location: ../views/perfil.php");
                exit;
            }

            echo "Error al registrar usuario.";

        } catch (PDOException $e) {
            echo "Error en base de datos: " . $e->getMessage();
        }
    }

    /* =====================================================
       ===================== LOGIN ==========================
       ===================================================== */
if ($action === "login") {
    try {
        if (empty($_POST['correo']) || empty($_POST['password'])) {
            die("Correo y contraseña obligatorios.");
        }

        $correo = $_POST['correo'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":correo", $correo);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("El usuario no existe.");
        }

        if (!password_verify($password, $user['contraseña'])) {
            die("Contraseña incorrecta.");
        }

        // =============================
        //   GUARDAR VARIABLES GLOBALES
        // =============================
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        $_SESSION['usuario_correo'] = $user['correo'];
        $_SESSION['usuario_telefono'] = $user['telefono'];
        $_SESSION['usuario_direccion'] = $user['direccion'];
        $_SESSION['usuario_rol'] = $user['rol'];

        if ($user['rol'] === 'admin') {
            header("Location: ../views/admin/administrar.html");
        }
        else if ($user['rol'] === 'cliente') {
            header("Location: ../views/perfil.php");
        } else {
            header("Location: ../views/index.html");
        }
        exit;

    } catch (PDOException $e) {
        echo "Error en base de datos: " . $e->getMessage();
    }
}
}

echo "Acceso no permitido.";
