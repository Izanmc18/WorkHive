<?php
require_once 'ConexionBD.php';

class RepositorioSolicitudes {
    private static $instancia = null;
    private $bd;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioSolicitudes();
        }
        return self::$instancia;
    }

    public function crear($idOferta, $idAlumno, $comentario) {
        $sql = "INSERT INTO solicitudes (id_oferta, id_alumno, comentario) VALUES (:idOferta, :idAlumno, :comentario)";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute([
            'idOferta' => $idOferta,
            'idAlumno' => $idAlumno,
            'comentario' => $comentario
        ]);
    }

    public function leerPorAlumno($idAlumno) {
        $sql = "SELECT * FROM solicitudes WHERE id_alumno = :idAlumno";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['idAlumno' => $idAlumno]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function modificarEstado($idSolicitud, $estado) {
        $sql = "UPDATE solicitudes SET estado = :estado WHERE id_solicitud = :idSolicitud";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['estado' => $estado, 'idSolicitud' => $idSolicitud]);
    }

    public function borrar($idSolicitud) {
        $sql = "DELETE FROM solicitudes WHERE id_solicitud = :idSolicitud";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idSolicitud' => $idSolicitud]);
    }
}
?>
