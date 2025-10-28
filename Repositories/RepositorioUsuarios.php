<?php
require_once 'ConexionBD.php';

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
        return $consulta->execute(['correo' => $correo, 'password' => password_hash($clave, PASSWORD_BCRYPT)]);
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
        return $consulta->execute(['password' => password_hash($nuevaClave, PASSWORD_BCRYPT), 'idUsuario' => $idUsuario]);
    }

    public function borrar($idUsuario) {
        $sql = "DELETE FROM usuarios WHERE id_user = :idUsuario";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idUsuario' => $idUsuario]);
    }
}
?>
