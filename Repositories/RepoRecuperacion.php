<?php
require_once 'ConexionBD.php';

class RepoRecuperacion {
    private static $instancia = null;
    private $bd;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepoRecuperacion();
        }
        return self::$instancia;
    }

    /* Guardamos en la BD la peticion de recuperacion de la contraseña*/
    public function crear($idUsuario, $idToken) {
        $sql = "INSERT INTO recuperacion_password (id_user, id_token, fecha_solicitud) 
                VALUES (:idUsuario, :idToken, :fechaSolicitud)";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute([
            'idUsuario' => $idUsuario,
            'idToken' => $idToken,
            'fechaSolicitud' => date('Y-m-d H:i:s')
        ]);
    }

    /* Buscar la recuperación por id_token en la BD */
    public function buscarPorToken($idToken) {
        $sql = "SELECT * FROM recuperacion_password WHERE id_token = :idToken";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['idToken' => $idToken]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Marcar uso del proceso (actualizar fecha)
    public function marcarUso($idRecuperacion) {
        $sql = "UPDATE recuperacion_password SET fecha_uso = :fechaUso WHERE id_recuperacion = :idRecuperacion";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute([
            'fechaUso' => date('Y-m-d H:i:s'),
            'idRecuperacion' => $idRecuperacion
        ]);
    }
}
?>
