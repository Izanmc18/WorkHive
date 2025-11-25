<?php

namespace App\Repositories;

use App\Repositories\ConexionBD;
use App\Repositories\RepositorioUsuarios; 
use App\Models\Empresa;
use App\Models\Usuario;
use PDO;

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

            $usuarioCreado = $this->repoUsuarios->crear($usuario);
            $empresa->setIdUsuario($usuarioCreado->getId());

            $sql = "INSERT INTO empresas (id_user, nombre, descripcion, logo_url, direccion, validacion)
                     VALUES (:id_user, :nombre, :descripcion, '', :direccion, :validacion)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':id_user' => $empresa->getIdUsuario(),
                ':nombre' => $empresa->getNombre(),
                ':descripcion' => $empresa->getDescripcion(),
                ':direccion' => $empresa->getDireccion(),
                ':validacion' => $empresa->getValidacion()
            ]);
            $idEmpresa = $this->bd->lastInsertId();
            $empresa->setIdEmpresa($idEmpresa);

            if ($archivoLogo && $archivoLogo['error'] === UPLOAD_ERR_OK) {
                
                $extension = pathinfo($archivoLogo['name'], PATHINFO_EXTENSION);
                $nombreArchivo = $idEmpresa . '.' . $extension;
                
                $directorioBase = dirname(__DIR__, 1); 
                $directorioDestino = $directorioBase . '/Public/Assets/Images/Empresa/';
                $rutaDestino = $directorioDestino . $nombreArchivo;

                if (!is_dir($directorioDestino)) {
                    if (!mkdir($directorioDestino, 0777, true)) {
                        throw new \Exception("Error (Permisos): Falló mkdir.");
                    }
                }
                
                if (move_uploaded_file($archivoLogo['tmp_name'], $rutaDestino)) {
                    
                    $sqlUpdate = "UPDATE empresas SET logo_url = :logo_url WHERE id_empresa = :id_empresa";
                    $stmtUpdate = $this->bd->prepare($sqlUpdate);
                    $stmtUpdate->execute([
                        ':logo_url' => $nombreArchivo,
                        ':id_empresa' => $idEmpresa
                    ]);
                    $empresa->setLogoUrl($nombreArchivo);
                } else {
                    error_log("Error al mover el archivo.");
                }

            }

            $this->bd->commit();
            return $empresa;
        } catch (\Exception $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            throw $e;
        }
    }

    public function leer($idEmpresa) {
        $sql = "SELECT e.*, u.correo FROM empresas e JOIN usuarios u ON e.id_user = u.id_user WHERE e.id_empresa = ?";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute([$idEmpresa]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
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
            $fila["direccion"],
            $fila["validacion"]
        );
    }
    
    public function obtenerPorIdUsuario(int $idUsuario) : ?Empresa {
        $sql = "SELECT id_empresa FROM empresas WHERE id_user = :id_user";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id_user' => $idUsuario]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fila) {
            return null;
        }
        return $this->leer((int)$fila['id_empresa']);
    }

    public function editar(Empresa $empresa, Usuario $usuario, $archivoLogo = null) {
        try {
            $this->bd->beginTransaction();

            $this->repoUsuarios->editar($usuario);

            $sql = "UPDATE empresas SET nombre = :nombre, descripcion = :descripcion, direccion = :direccion, validacion = :validacion WHERE id_empresa = :id_empresa";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':nombre' => $empresa->getNombre(),
                ':descripcion' => $empresa->getDescripcion(),
                ':direccion' => $empresa->getDireccion(),
                ':validacion' => $empresa->getValidacion(),
                ':id_empresa' => $empresa->getIdEmpresa()
            ]);

            if ($archivoLogo && $archivoLogo['error'] === UPLOAD_ERR_OK) {
                
                $directorioBase = dirname(__DIR__, 1); 
                $directorioDestino = $directorioBase . '/Public/Assets/Images/Empresa/';
                
                $extension = pathinfo($archivoLogo['name'], PATHINFO_EXTENSION);
                $nombreArchivo = $empresa->getIdEmpresa() . '.' . $extension;
                $rutaDestino = $directorioDestino . $nombreArchivo;

                if (!is_dir($directorioDestino)) {
                    if (!mkdir($directorioDestino, 0777, true)) {
                        throw new \Exception("Error (Permisos): No se pudo crear el directorio de logos.");
                    }
                }
                
                if ($empresa->getLogoUrl()) {
                    $rutaLogoAntiguo = $directorioDestino . $empresa->getLogoUrl();
                    if (file_exists($rutaLogoAntiguo)) {
                        @unlink($rutaLogoAntiguo);
                    }
                }

                if (move_uploaded_file($archivoLogo['tmp_name'], $rutaDestino)) {
                    
                    $sqlUpdateLogo = "UPDATE empresas SET logo_url = :logo_url WHERE id_empresa = :id_empresa";
                    $stmtUpdateLogo = $this->bd->prepare($sqlUpdateLogo);
                    $stmtUpdateLogo->execute([
                        ':logo_url' => $nombreArchivo,
                        ':id_empresa' => $empresa->getIdEmpresa()
                    ]);
                    $empresa->setLogoUrl($nombreArchivo);
                } else {
                    throw new \Exception("Error al guardar el logo.");
                }
            }

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            throw $e;
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
            
            if ($empresa->getLogoUrl()) {
                $directorioBase = dirname(__DIR__, 1);
                $rutaLogo = $directorioBase . '/Public/Assets/Images/Empresa/' . $empresa->getLogoUrl();
                if (file_exists($rutaLogo)) {
                    @unlink($rutaLogo);
                }
            }

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
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $empresas[] = $this->leer($fila["id_empresa"]);
        }
        return array_filter($empresas); 
    }

    public function buscarPorNombre(string $texto) {
        $sql = "SELECT e.id_empresa FROM empresas e WHERE e.nombre LIKE :texto";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':texto' => '%' . $texto . '%']);
        $empresas = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $empresas[] = $this->leer($fila['id_empresa']);
        }
        return array_filter($empresas);
    }

    public function findById(int $idEmpresa) {
        return $this->leer($idEmpresa);
    }

    public function listarEmpresasValidadas() {
        $sql = "SELECT e.id_empresa FROM empresas e WHERE e.validacion = 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $empresas = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $empresas[] = $this->leer($fila['id_empresa']);
        }
        return array_filter($empresas);
    }

    public function listarEmpresasPendientes() {
        $sql = "SELECT e.id_empresa FROM empresas e WHERE e.validacion = 0";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $empresas = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $empresas[] = $this->leer($fila['id_empresa']);
        }
        return array_filter($empresas);
    }

    public function aprobarEmpresa(int $idEmpresa) {
        $sql = "UPDATE empresas SET validacion = 1 WHERE id_empresa = :id_empresa";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id_empresa' => $idEmpresa]);
    }

    public function contarTotalEmpresas() {
        $sql = "SELECT COUNT(*) as total FROM empresas";
        $stmt = $this->bd->query($sql);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['total'] : 0;
    }

    public function contarEmpresasPendientes() {
        $sql = "SELECT COUNT(*) as total FROM empresas WHERE validacion = 0";
        $stmt = $this->bd->query($sql);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['total'] : 0;
    }
}