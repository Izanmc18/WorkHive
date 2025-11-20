<?php

namespace App\Repositories;

use App\Repositories\ConexionBD;
use App\Repositories\RepositorioUsuarios;
use App\Models\Alumno;
use App\Models\Usuario;

class RepositorioAlumnos {
    private $bd;
    private static $instancia;
    private $repoUsuarios;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
        $this->repoUsuarios = RepositorioUsuarios::getInstancia();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioAlumnos();
        }
        return self::$instancia;
    }

    /**
     * Crear alumno modificado: BD primero, Archivos después (sin rollback por archivos).
     */
    public function create(Alumno $alumno, Usuario $usuario, $archivoFoto = null, $archivoCurriculum = null) {
        try {
            $this->bd->beginTransaction();

            $usuarioCreado = $this->repoUsuarios->crear($usuario);
            $alumno->setIdUsuario($usuarioCreado->getId());

            $sql = "INSERT INTO alumnos (id_user, nombre, apellido1, apellido2, direccion, edad, curriculum_url, foto_perfil)
                    VALUES (:iduser, :nombre, :apellido1, :apellido2, :direccion, :edad, '', '')";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':iduser'      => $alumno->getIdUsuario(),
                ':nombre'      => $alumno->getNombre(),
                ':apellido1'   => $alumno->getApellido1(),
                ':apellido2'   => $alumno->getApellido2(),
                ':direccion'   => $alumno->getDireccion(),
                ':edad'        => $alumno->getEdad(),
            ]);
            $idAlumno = $this->bd->lastInsertId();
            $alumno->setIdAlumno($idAlumno);

            $this->bd->commit();

        } catch (\Exception $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            error_log("Error de Transacción al guardar Alumno (SQL): " . $e->getMessage());
            throw $e;
        }

        
        $directorioBase = dirname(__DIR__, 1); 
        $updates = [];
        $params = [':id' => $idAlumno];

        try {
            
            if ($archivoFoto && $archivoFoto['error'] === UPLOAD_ERR_OK) {
                $extensionFoto = pathinfo($archivoFoto['name'], PATHINFO_EXTENSION);
                $fotoNombre = $idAlumno . '.' . $extensionFoto; 
                $directorioFoto = $directorioBase . '/Public/Assets/Images/';
                
                if (!is_dir($directorioFoto)) {
                    mkdir($directorioFoto, 0777, true);
                }
                
                if (move_uploaded_file($archivoFoto['tmp_name'], $directorioFoto . $fotoNombre)) {
                    $updates[] = 'foto_perfil = :foto_perfil';
                    $params[':foto_perfil'] = $fotoNombre;
                    $alumno->setFotoPerfil($fotoNombre);
                } else {
                    error_log("Error: No se pudo mover la foto para Alumno ID $idAlumno");
                }
            }

            
            if ($archivoCurriculum && $archivoCurriculum['error'] === UPLOAD_ERR_OK) {
                $extensionCurr = pathinfo($archivoCurriculum['name'], PATHINFO_EXTENSION);
                
                if (strtolower($extensionCurr) === 'pdf') {
                    $curriculumNombre = $idAlumno . '.pdf';
                    $directorioCurr = $directorioBase . '/Data/';
                    
                    if (!is_dir($directorioCurr)) {
                        mkdir($directorioCurr, 0777, true);
                    }

                    if (move_uploaded_file($archivoCurriculum['tmp_name'], $directorioCurr . $curriculumNombre)) {
                        $updates[] = 'curriculum_url = :curriculum_url';
                        $params[':curriculum_url'] = $curriculumNombre;
                        $alumno->setCurriculumUrl($curriculumNombre);
                    } else {
                        error_log("Error: No se pudo mover el CV para Alumno ID $idAlumno");
                    }
                }
            }

            
            if (!empty($updates)) {
                $sqlUpdate = "UPDATE alumnos SET " . implode(', ', $updates) . " WHERE id_alumno = :id";
                $stmtUpdate = $this->bd->prepare($sqlUpdate);
                $stmtUpdate->execute($params);
            }

        } catch (\Exception $eFile) {
            
            error_log("Error post-creación archivos Alumno: " . $eFile->getMessage());
        }

        return $alumno;
    }

    /**
     * Edita un alumno existente y sus archivos (Lógica separada).
     */
    public function save(Alumno $alumno, Usuario $usuario, $archivoFoto = null, $archivoCurriculum = null) {
        // PASO 1: ACTUALIZACIÓN DATOS
        try {
            $this->bd->beginTransaction();

            $this->repoUsuarios->editar($usuario);

            $sql = "UPDATE alumnos SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2,
                direccion = :direccion, edad = :edad WHERE id_alumno = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':nombre'      => $alumno->getNombre(),
                ':apellido1'   => $alumno->getApellido1(),
                ':apellido2'   => $alumno->getApellido2(),
                ':direccion'   => $alumno->getDireccion(),
                ':edad'        => $alumno->getEdad(),
                ':id'          => $alumno->getIdAlumno()
            ]);

            $this->bd->commit();

        } catch (\Exception $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return false;
        }

        try {
            $directorioBase = dirname(__DIR__, 1);
            $updates = [];
            $params = [':id' => $alumno->getIdAlumno()];

            if ($archivoFoto && $archivoFoto['error'] === UPLOAD_ERR_OK) {
                $extensionFoto = pathinfo($archivoFoto['name'], PATHINFO_EXTENSION);
                $fotoNombre = $alumno->getIdAlumno() . '.' . $extensionFoto;
                $directorioFoto = $directorioBase . '/Public/Assets/Images/';
                
                if (!is_dir($directorioFoto)) mkdir($directorioFoto, 0777, true);

                if ($alumno->getFotoPerfil() && file_exists($directorioFoto . $alumno->getFotoPerfil())) {
                    @unlink($directorioFoto . $alumno->getFotoPerfil());
                }

                if (move_uploaded_file($archivoFoto['tmp_name'], $directorioFoto . $fotoNombre)) {
                    $updates[] = 'foto_perfil = :foto_perfil';
                    $params[':foto_perfil'] = $fotoNombre;
                    $alumno->setFotoPerfil($fotoNombre);
                }
            }

            if ($archivoCurriculum && $archivoCurriculum['error'] === UPLOAD_ERR_OK) {
                $extensionCurr = pathinfo($archivoCurriculum['name'], PATHINFO_EXTENSION);
                if (strtolower($extensionCurr) === 'pdf') {
                    $curriculumNombre = $alumno->getIdAlumno() . '.pdf';
                    $directorioCurr = $directorioBase . '/Data/';
                    
                    if (!is_dir($directorioCurr)) mkdir($directorioCurr, 0777, true);

                    if ($alumno->getCurriculumUrl() && file_exists($directorioCurr . $alumno->getCurriculumUrl())) {
                        @unlink($directorioCurr . $alumno->getCurriculumUrl());
                    }
                    
                    if (move_uploaded_file($archivoCurriculum['tmp_name'], $directorioCurr . $curriculumNombre)) {
                        $updates[] = 'curriculum_url = :curriculum_url';
                        $params[':curriculum_url'] = $curriculumNombre;
                        $alumno->setCurriculumUrl($curriculumNombre);
                    }
                }
            }


            if (!empty($updates)) {
                $sqlUpdate = "UPDATE alumnos SET " . implode(', ', $updates) . " WHERE id_alumno = :id";
                $stmtUpdate = $this->bd->prepare($sqlUpdate);
                $stmtUpdate->execute($params);
            }

        } catch (\Exception $eFile) {
             error_log("Error actualizando archivos Alumno: " . $eFile->getMessage());
             
        }

        return true;
    }

    public function leer($idAlumno) {
        $sql = "SELECT a.*, u.correo 
                FROM alumnos a 
                JOIN usuarios u ON a.id_user = u.id_user 
                WHERE a.id_alumno = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idAlumno]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$fila) return null;

        return new Alumno(
            $fila['id_alumno'],
            $fila['id_user'],
            $fila['correo'], 
            $fila['nombre'],
            $fila['apellido1'],
            $fila['apellido2'],
            $fila['direccion'],
            $fila['edad'],
            $fila['curriculum_url'],
            $fila['foto_perfil']
        );
    }

    public function delete($idAlumno) {
        $alumno = $this->leer($idAlumno);
        if (!$alumno) return false;

        try {
            $this->bd->beginTransaction();

            $sql = "DELETE FROM alumnos WHERE id_alumno = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idAlumno]);

            $this->repoUsuarios->borrar($alumno->getIdUsuario());

            $this->bd->commit();
            
            
            $directorioBase = dirname(__DIR__, 1);
            
            if ($alumno->getFotoPerfil()) {
                $rutaFoto = $directorioBase . '/Public/Assets/Images/' . $alumno->getFotoPerfil();
                if (file_exists($rutaFoto)) @unlink($rutaFoto);
            }
            if ($alumno->getCurriculumUrl()) {
                $rutaCv = $directorioBase . '/Data/' . $alumno->getCurriculumUrl();
                if (file_exists($rutaCv)) @unlink($rutaCv);
            }

            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function listar() {
        $sql = "SELECT a.*, u.correo 
                FROM alumnos a 
                JOIN usuarios u ON a.id_user = u.id_user";
        $stmt = $this->bd->query($sql);
        $alumnos = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $alumnos[] = new Alumno(
                $fila['id_alumno'],
                $fila['id_user'],
                $fila['correo'],
                $fila['nombre'],
                $fila['apellido1'],
                $fila['apellido2'],
                $fila['direccion'],
                $fila['edad'],
                $fila['curriculum_url'],
                $fila['foto_perfil']
            );
        }
        return $alumnos;
    }

    public function findById(int $idAlumno) {
        return $this->leer($idAlumno);
    }

    public function buscarPorNombre(string $texto) {
        $sql = "SELECT a.*, u.correo 
                FROM alumnos a 
                JOIN usuarios u ON a.id_user = u.id_user 
                WHERE a.nombre LIKE :texto";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':texto' => '%' . $texto . '%']);
        $alumnos = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $alumnos[] = new Alumno(
                $fila['id_alumno'],
                $fila['id_user'],
                $fila['correo'],        
                $fila['nombre'],
                $fila['apellido1'],
                $fila['apellido2'],
                $fila['direccion'],
                $fila['edad'],
                $fila['curriculum_url'],
                $fila['foto_perfil']
            );
        }
        return $alumnos;
    }
    
    public function obtenerPorIdUsuario(int $idUsuario) : ?Alumno {
        $sql = "SELECT id_alumno FROM alumnos WHERE id_user = :id_user";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id_user' => $idUsuario]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$fila) {
            return null;
        }
        return $this->leer((int)$fila['id_alumno']);
    }
}