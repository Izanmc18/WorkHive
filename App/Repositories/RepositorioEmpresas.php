<?php
namespace App\Repositories;

require_once __DIR__ . '/ConexionBD.php';
require_once __DIR__ . '/RepositorioUsuarios.php';
use App\Models\Empresa;
use App\Models\Usuario;

class RepositorioEmpresas {
    private $bd;
    private $repoUsuarios;

    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
        $this->repoUsuarios = new RepositorioUsuarios();
    }

    public function crear(Empresa $empresa, Usuario $usuario) {
        try {
            $this->bd->beginTransaction();

            $usuarioCreado = $this->repoUsuarios->crear($usuario);
            $empresa->setIdUsuario($usuarioCreado->getId());

            $sql = "INSERT INTO empresas (id_user, nombre, descripcion, logo_url, direccion)
                    VALUES (:id_user, :nombre, :descripcion, :logo_url, :direccion)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':id_user'     => $empresa->getIdUsuario(),
                ':nombre'      => $empresa->getNombre(),
                ':descripcion' => $empresa->getDescripcion(),
                ':logo_url'    => $empresa->getLogoUrl(),
                ':direccion'   => $empresa->getDireccion()
            ]);

            $empresa->setIdEmpresa($this->bd->lastInsertId());
            $this->bd->commit();
            return $empresa;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            throw $e;
        }
    }

    public function leer($id_Empresa) {
        $sql = "SELECT e.*, u.correo FROM empresas e JOIN usuarios u ON e.id_user = u.id_user WHERE e.id_empresa = ?";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute([$id_Empresa]);
        return $consulta->fetch(\PDO::FETCH_ASSOC);
    }

    public function editar($idEmpresa, $correo, $nombre, $descripcion, $logoUrl, $direccion) {
        try {
            $this->bd->beginTransaction();
            // Obtener la empresa y su usuario
            $empresa = $this->leer($idEmpresa);
            if (!$empresa) {
                $this->bd->rollBack();
                return false;
            }
            // Actualizar usuario (correo)
            $repoUsuarios = RepositorioUsuarios::getInstancia();
            $repoUsuarios->modificarCorreo($empresa['id_user'], $correo);

            // Actualizar empresa
            $sql = "UPDATE empresas SET nombre = :nombre, descripcion = :descripcion, logo_url = :logoUrl, direccion = :direccion
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
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }


    
    public function borrar($idEmpresa) {
    $empresa = $this->leer($idEmpresa);
    if (!$empresa) {
        throw new \Exception("No se encontró la empresa con id $idEmpresa");
    }
    try {
        $this->bd->beginTransaction();

        $sql = "DELETE FROM empresas WHERE id_empresa = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idEmpresa]);

        $this->repoUsuarios->borrar($empresa->getIdUsuario());

        $this->bd->commit();
        return true;
    } catch (\Exception $e) {
        $this->bd->rollBack();
        throw new \Exception("Fallo en borrar: " . $e->getMessage());
    }
}

    public function listar() {
        $sql = "SELECT * FROM empresas";
        $stmt = $this->bd->query($sql);
        $empresas = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $empresas[] = [
                'id_empresa'  => $fila['id_empresa'],
                'id_user'     => $fila['id_user'],
                'nombre'      => $fila['nombre'],
                'descripcion' => $fila['descripcion'],
                'logo_url'    => $fila['logo_url'],
                'direccion'   => $fila['direccion']
            ];
        }
        return $empresas;
    }
}
