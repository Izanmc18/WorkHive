<?php
require_once 'ConexionBD.php';
require_once '../Helpers/Security/PasswordSecurity.php';

class RepositorioUsuarios {
    private static $instancia = null;
    private $bd;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioUsuarios();
        }
        return self::$instancia;
    }

    public function crear($correo, $clave) {
        $sql = "INSERT INTO usuarios (correo, password) VALUES (:correo, :password)";
        $consulta = $this->bd->prepare($sql);
        $hash = PasswordSecurity::encriptar($clave);
        $consulta->execute(['correo' => $correo, 'password' => $hash]);
        return $this->bd->lastInsertId();
    }

    public function borrar($idUsuario) {
        $sql = "DELETE FROM usuarios WHERE id_user = :idUsuario";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idUsuario' => $idUsuario]);
    }

    public function buscarPorCorreo($correo) {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['correo' => $correo]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function modificarClave($idUsuario, $nuevaClave) {
        $sql = "UPDATE usuarios SET password = :password WHERE id_user = :idUsuario";
        $consulta = $this->bd->prepare($sql);
        $hash = PasswordSecurity::encriptar($nuevaClave);
        return $consulta->execute(['password' => $hash, 'idUsuario' => $idUsuario]);
    }

}
?>
