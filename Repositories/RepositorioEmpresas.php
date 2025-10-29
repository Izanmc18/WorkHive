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

    public function crear($correo, $clave, $nombre, $descripcion, $logoUrl, $direccion) {
        try {
            $this->bd->beginTransaction();

            $repoUsuarios = RepositorioUsuarios::getInstancia();
            $idUsuario = $repoUsuarios->crear($correo, $clave);

            $sql = "INSERT INTO empresas (id_user, nombre, descripcion, logo_url, direccion) 
                    VALUES (:idUsuario, :nombre, :descripcion, :logoUrl, :direccion)";
            $consulta = $this->bd->prepare($sql);
            $consulta->execute([
                'idUsuario' => $idUsuario,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'logoUrl' => $logoUrl,
                'direccion' => $direccion
            ]);

            $this->bd->commit();
            return true;
        } catch (Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function borrar($idEmpresa) {
        try {
            $this->bd->beginTransaction();

            $empresa = $this->leer($idEmpresa);
            if (!$empresa) {
                $this->bd->rollBack();
                return false;
            }

            $sql = "DELETE FROM empresas WHERE id_empresa = :idEmpresa";
            $consulta = $this->bd->prepare($sql);
            $consulta->execute(['idEmpresa' => $idEmpresa]);

            $repoUsuarios = RepositorioUsuarios::getInstancia();
            $repoUsuarios->borrar($empresa['id_user']);

            $this->bd->commit();
            return true;
        } catch (Exception $e) {
            $this->bd->rollBack();
            return false;
        }
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


    public function filtrarEmpresas($busqueda = '', $ordenarPor = 'nombre', $direccionOrden = 'ASC') {
        $camposValidos = ['nombre', 'direccion'];
        $ordenarPor = in_array($ordenarPor, $camposValidos) ? $ordenarPor : 'nombre';
        $direccionOrden = strtoupper($direccionOrden) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM empresas WHERE 1=1";
        $parametros = [];

        if (!empty($busqueda)) {
            $sql .= " AND nombre LIKE :busqueda";
            $parametros['busqueda'] = "%" . $busqueda . "%";
        }
        $sql .= " ORDER BY $ordenarPor $direccionOrden";

        $consulta = $this->bd->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verFicha($idEmpresa) {
        $sql = "SELECT nombre, descripcion, logo_url FROM empresas WHERE id_empresa = :idEmpresa";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['idEmpresa' => $idEmpresa]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function editar($idEmpresa, $nombre, $descripcion, $logoUrl, $direccion) {
        try {
            $this->bd->beginTransaction();

            $sql = "UPDATE empresas 
                    SET nombre = :nombre, descripcion = :descripcion, logo_url = :logoUrl, direccion = :direccion 
                    WHERE id_empresa = :idEmpresa";
            $consulta = $this->bd->prepare($sql);
            $consulta->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'logoUrl' => $logoUrl,
                'direccion' => $direccion,
                'idEmpresa' => $idEmpresa
            ]);

            $this->bd->commit();
            return true;
        } catch(Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

}
?>
