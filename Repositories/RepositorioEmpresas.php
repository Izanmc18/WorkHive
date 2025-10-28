<?php
require_once 'ConexionBD.php';

class RepositorioEmpresas {
    private static $instancia = null;
    private $bd;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioEmpresas();
        }
        return self::$instancia;
    }

    public function crear($idUsuario, $nombre, $descripcion, $logoUrl, $direccion) {
        $sql = "INSERT INTO empresas (id_user, nombre, descripcion, logo_url, direccion) VALUES (:idUsuario, :nombre, :descripcion, :logoUrl, :direccion)";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute([
            'idUsuario' => $idUsuario,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'logoUrl' => $logoUrl,
            'direccion' => $direccion
        ]);
    }

    public function leer($idEmpresa) {
        $sql = "SELECT * FROM empresas WHERE id_empresa = :idEmpresa";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['idEmpresa' => $idEmpresa]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function modificarDescripcion($idEmpresa, $nuevaDescripcion) {
        $sql = "UPDATE empresas SET descripcion = :nuevaDescripcion WHERE id_empresa = :idEmpresa";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['nuevaDescripcion' => $nuevaDescripcion, 'idEmpresa' => $idEmpresa]);
    }

    public function borrar($idEmpresa) {
        $sql = "DELETE FROM empresas WHERE id_empresa = :idEmpresa";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idEmpresa' => $idEmpresa]);
    }
}
?>
