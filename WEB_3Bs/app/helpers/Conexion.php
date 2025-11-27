<?php
class Conexion {
    private $host = "localhost";    
    private $db   = "base_3bs";      
    private $user = "root";          
    private $pass = "";              
    public $conexion;

    public function conectar() {
        $this->conexion = null;

        try {
            $this->conexion = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=utf8",
                $this->user,
                $this->pass
            );
            
            // Opciones recomendadas
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Muestra error controlado
            echo "Error de conexión: " . $e->getMessage();
        }

        return $this->conexion;
    }
}
?>
