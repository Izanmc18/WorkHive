<?php
require_once 'ConexionBD.php';
require_once '../Helpers/Security/TokenSecurity.php';

class RepositorioTokens {
    private static $instancia = null;
    private $bd;
    private $seguridad;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
        $this->seguridad = new TokenSecurity(); // puedes pasar duración aquí
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioTokens();
        }
        return self::$instancia;
    }

    public function crear($idUsuario) {
        $datos = $this->seguridad->generarToken($idUsuario);
        $sql = "INSERT INTO tokens (id_user, token, fecha_creacion, fecha_expiracion)
                VALUES (:idUsuario, :token, :fechaGeneracion, :fechaExpiracion)";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute([
            'idUsuario' => $idUsuario,
            'token' => $datos['token'],
            'fechaGeneracion' => $datos['fechaGeneracion'],
            'fechaExpiracion' => $datos['fechaExpiracion']
        ]);
        return $datos['token'];
    }

    public function verificar($tokenRecibido) {
        $sql = "SELECT * FROM tokens WHERE token = :token";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['token' => $tokenRecibido]);
        $tokenBD = $consulta->fetch(PDO::FETCH_ASSOC);
        if (!$tokenBD) return false;
        return $this->seguridad->verificarToken($tokenBD, $tokenRecibido);
    }

    public function borrar($idToken) {
        $sql = "DELETE FROM tokens WHERE id_token = :idToken";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idToken' => $idToken]);
    }
}
?>
