<?php
require_once 'ConexionBD.php';

class RepositorioOfertas {
    private static $instancia = null;
    private $bd;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioOfertas();
        }
        return self::$instancia;
    }

    public function crear($idEmpresa, $descripcion, $fechaInicio, $fechaFin, $activa, $titulo) {
        $sql = "INSERT INTO ofertas (id_empresa, descripcion, fecha_inicio, fecha_fin, activa, titulo) VALUES (:idEmpresa, :descripcion, :fechaInicio, :fechaFin, :activa, :titulo)";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute([
            'idEmpresa' => $idEmpresa,
            'descripcion' => $descripcion,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'activa' => $activa,
            'titulo' => $titulo
        ]);
    }

    public function leer($idOferta) {
        $sql = "SELECT * FROM ofertas WHERE id_oferta = :idOferta";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['idOferta' => $idOferta]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function modificarActivo($idOferta, $activa) {
        $sql = "UPDATE ofertas SET activa = :activa WHERE id_oferta = :idOferta";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['activa' => $activa, 'idOferta' => $idOferta]);
    }

    public function borrar($idOferta) {
        $sql = "DELETE FROM ofertas WHERE id_oferta = :idOferta";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idOferta' => $idOferta]);
    }
}
?>
