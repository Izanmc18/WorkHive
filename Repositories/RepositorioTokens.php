<?php
require_once 'ConexionBD.php';

class RepositorioTokens {
    private static $instancia = null;
    private $bd;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioTokens();
        }
        return self::$instancia;
    }

    public function crear($idUsuario, $token, $fechaExpiracion = null) {
        $sql = "INSERT INTO tokens (id_user, token, fecha_expiracion) VALUES (:idUsuario, :token, :fechaExpiracion)";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute([
            'idUsuario' => $idUsuario,
            'token' => $token,
            'fechaExpiracion' => $fechaExpiracion
        ]);
    }

    public function buscarPorToken($token) {
        $sql = "SELECT * FROM tokens WHERE token = :token";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['token' => $token]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function borrar($idToken) {
        $sql = "DELETE FROM tokens WHERE id_token = :idToken";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idToken' => $idToken]);
    }
}
?>
