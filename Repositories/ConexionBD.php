<?php
class ConexionBD {
    private static $instancia = null;
    private $conexion;

    private function __construct() {
        $host = "localhost";
        $db = "portal_empleo";
        $usuario = "root";
        $clave = "1234";
        $this->conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $usuario, $clave, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new ConexionBD();
        }
        return self::$instancia;
    }

    public function getConexion() {
        return $this->conexion;
    }
}
?>
