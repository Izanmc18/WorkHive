<?php

namespace App\Repositories;

use App\Models\Empresa;
use App\Models\Usuario;

class RepositorioEmpresas {
    private $bd;
    private static $instancia;
    private $repoUsuarios;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
        $this->repoUsuarios = RepositorioUsuarios::getInstancia();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioEmpresas();
        }
        return self::$instancia;
    }

    public function crear(Empresa $empresa, Usuario $usuario, $archivoLogo = null) {
        try {
            $this->bd->beginTransaction();

            // Creo un usuario
            $usuarioCreado = $this->repoUsuarios->crear($usuario);
            $empresa->setIdUsuario($usuarioCreado->getId());

            // Inserto la empresa pero sin logo_url por ahora
            $sql = "INSERT INTO empresas (id_user, nombre, descripcion, logo_url, direccion)
                    VALUES (:id_user, :nombre, :descripcion, '', :direccion)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':id_user' => $empresa->getIdUsuario(),
                ':nombre' => $empresa->getNombre(),
                ':descripcion' => $empresa->getDescripcion(),
                ':direccion' => $empresa->getDireccion()
            ]);
            $idEmpresa = $this->bd->lastInsertId();
            $empresa->setIdEmpresa($idEmpresa);

            // Guardo el logo si está presente
            if ($archivoLogo && $archivoLogo['error'] === UPLOAD_ERR_OK) {
                $extension = pathinfo($archivoLogo['name'], PATHINFO_EXTENSION);
                $nombreArchivo = $idEmpresa . '.' . $extension;
                $directorioDestino = __DIR__ . '/../../Public/Assets/Images/Empresa';
                $rutaDestino = $directorioDestino . $nombreArchivo;

                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0755, true);
                }

                if (!move_uploaded_file($archivoLogo['tmp_name'], $rutaDestino)) {
                    throw new \Exception("Error al guardar el logo.");
                }

                // Actualizo el campo logo_url
                $sqlUpdate = "UPDATE empresas SET logo_url = :logo_url WHERE id_empresa = :id_empresa";
                $stmtUpdate = $this->bd->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':logo_url' => $nombreArchivo,
                    ':id_empresa' => $idEmpresa
                ]);
                $empresa->setLogoUrl($nombreArchivo);
            }

            $this->bd->commit();
            return $empresa;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            throw $e;
        }
    }

    public function leer($idEmpresa) {
        $sql = "SELECT e.*, u.correo FROM empresas e JOIN usuarios u ON e.id_user = u.id_user WHERE e.id_empresa = ?";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute([$idEmpresa]);
        $fila = $consulta->fetch(\PDO::FETCH_ASSOC);
        if (!$fila) {
            return null;
        }
        return new Empresa(
            $fila["id_empresa"],
            $fila["id_user"],
            $fila["correo"],
            $fila["nombre"],
            $fila["descripcion"],
            $fila["logo_url"],
            $fila["direccion"]
        );
    }

    public function editar(Empresa $empresa, Usuario $usuario, $archivoLogo = null) {
        try {
            $this->bd->beginTransaction();

            // Edito primero el usuario
            $this->repoUsuarios->editar($usuario);

            // Despues edito la empresa
            $sql = "UPDATE empresas SET nombre = :nombre, descripcion = :descripcion, direccion = :direccion WHERE id_empresa = :id_empresa";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':nombre' => $empresa->getNombre(),
                ':descripcion' => $empresa->getDescripcion(),
                ':direccion' => $empresa->getDireccion(),
                ':id_empresa' => $empresa->getIdEmpresa()
            ]);

            // Si hay un nuevo logo subido, lo que hago es reemplazar el archivo y actualizo en la BD
            if ($archivoLogo && $archivoLogo['error'] === UPLOAD_ERR_OK) {
                $extension = pathinfo($archivoLogo['name'], PATHINFO_EXTENSION);
                $nombreArchivo = $empresa->getIdEmpresa() . '.' . $extension;
                $directorioDestino = __DIR__ . '/../../Public/Assets/Images/Empresa';
                $rutaDestino = $directorioDestino . $nombreArchivo;

                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0755, true);
                }

                if (!move_uploaded_file($archivoLogo['tmp_name'], $rutaDestino)) {
                    throw new \Exception("Error al guardar el logo.");
                }

                $sqlUpdateLogo = "UPDATE empresas SET logo_url = :logo_url WHERE id_empresa = :id_empresa";
                $stmtUpdateLogo = $this->bd->prepare($sqlUpdateLogo);
                $stmtUpdateLogo->execute([
                    ':logo_url' => $nombreArchivo,
                    ':id_empresa' => $empresa->getIdEmpresa()
                ]);
                $empresa->setLogoUrl($nombreArchivo);
            }

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

            // Borro la empresa
            $sql = "DELETE FROM empresas WHERE id_empresa = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idEmpresa]);

            // Borro el usuario asociado a esa empresa
            $this->repoUsuarios->borrar($empresa->getIdUsuario());

            // Borro el archivo logo si existe
            if ($empresa->getLogoUrl()) {
                $rutaLogo = __DIR__ . '/../../Public/Assets/Images/' . $empresa->getLogoUrl();
                if (file_exists($rutaLogo)) {
                    unlink($rutaLogo);
                }
            }

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            throw new \Exception("Error al borrar empresa: " . $e->getMessage());
        }
    }

    public function listar() {
        $sql = "SELECT * FROM empresas";
        $stmt = $this->bd->query($sql);
        $empresas = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $empresas[] = new Empresa(
                $fila["id_empresa"],
                $fila["id_user"],
                null,
                $fila["nombre"],
                $fila["descripcion"],
                $fila["logo_url"],
                $fila["direccion"]
            );
        }
        return $empresas;
    }

    public function buscarPorNombre(string $texto) {
        $sql = "SELECT * FROM empresas WHERE nombre LIKE :texto";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':texto' => '%' . $texto . '%']);
        $empresas = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $empresas[] = new Empresa(
                $fila['idempresa'],
                $fila['idusuario'],
                $fila['correo'],
                $fila['nombre'],
                $fila['descripcion'],
                $fila['logourl'],
                $fila['direccion']
            );
        }
        return $empresas;
    }
}
