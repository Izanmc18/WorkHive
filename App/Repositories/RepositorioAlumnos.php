<?php

namespace App\Repositories;

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

    public function crear(Alumno $alumno, Usuario $usuario, $archivoFoto = null) {
        try {
            $this->bd->beginTransaction();

            // Crear usuario
            $usuarioCreado = $this->repoUsuarios->crear($usuario);
            $alumno->setIdUsuario($usuarioCreado->getId());

            // Insertar sin fotoPerfil por ahora
            $sql = "INSERT INTO alumnos (iduser, nombre, apellido1, apellido2, direccion, edad, curriculumurl, fotoperfil)
                    VALUES (:iduser, :nombre, :apellido1, :apellido2, :direccion, :edad, :curriculumurl, '')";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':iduser'        => $alumno->getIdUsuario(),
                ':nombre'        => $alumno->getNombre(),
                ':apellido1'     => $alumno->getApellido1(),
                ':apellido2'     => $alumno->getApellido2(),
                ':direccion'     => $alumno->getDireccion(),
                ':edad'          => $alumno->getEdad(),
                ':curriculumurl' => $alumno->getCurriculumUrl()
            ]);
            $idAlumno = $this->bd->lastInsertId();
            $alumno->setIdAlumno($idAlumno);

            // Guardar foto de perfil si se sube
            if ($archivoFoto && $archivoFoto['error'] === UPLOAD_ERR_OK) {
                $extension = pathinfo($archivoFoto['name'], PATHINFO_EXTENSION);
                $nombreFoto = $idAlumno . '.' . $extension;
                $directorioDestino = __DIR__ . '/../../Public/Assets/Images/';
                $rutaDestino = $directorioDestino . $nombreFoto;

                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0755, true);
                }

                if (!move_uploaded_file($archivoFoto['tmp_name'], $rutaDestino)) {
                    throw new \Exception("Error al guardar la foto de perfil.");
                }

                $sqlUpdate = "UPDATE alumnos SET fotoperfil = :fotoperfil WHERE idalumno = :id";
                $stmtUpdate = $this->bd->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':fotoperfil' => $nombreFoto,
                    ':id' => $idAlumno
                ]);
                $alumno->setFotoPerfil($nombreFoto);
            }

            $this->bd->commit();
            return $alumno;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            throw $e;
        }
    }

    public function leer($idAlumno) {
        $sql = "SELECT * FROM alumnos WHERE idalumno = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idAlumno]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$fila) {
            return null;
        }
        return new Alumno(
            $fila['idalumno'],
            $fila['iduser'],
            null,
            $fila['nombre'],
            $fila['apellido1'],
            $fila['apellido2'],
            $fila['direccion'],
            $fila['edad'],
            $fila['curriculumurl'],
            $fila['fotoperfil']
        );
    }

    public function editar(Alumno $alumno, Usuario $usuario, $archivoFoto = null) {
        try {
            $this->bd->beginTransaction();

            // Editar usuario
            $this->repoUsuarios->editar($usuario);

            // Editar alumno
            $sql = "UPDATE alumnos SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2,
                direccion = :direccion, edad = :edad, curriculumurl = :curriculumurl WHERE idalumno = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':nombre'        => $alumno->getNombre(),
                ':apellido1'     => $alumno->getApellido1(),
                ':apellido2'     => $alumno->getApellido2(),
                ':direccion'     => $alumno->getDireccion(),
                ':edad'          => $alumno->getEdad(),
                ':curriculumurl' => $alumno->getCurriculumUrl(),
                ':id'            => $alumno->getIdAlumno()
            ]);

            // Nueva foto subida
            if ($archivoFoto && $archivoFoto['error'] === UPLOAD_ERR_OK) {
                $extension = pathinfo($archivoFoto['name'], PATHINFO_EXTENSION);
                $nombreFoto = $alumno->getIdAlumno() . '.' . $extension;
                $directorioDestino = __DIR__ . '/../../Public/Assets/Images/';
                $rutaDestino = $directorioDestino . $nombreFoto;

                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0755, true);
                }
                if (!move_uploaded_file($archivoFoto['tmp_name'], $rutaDestino)) {
                    throw new \Exception("Error al guardar la foto de perfil.");
                }

                $sqlUpdate = "UPDATE alumnos SET fotoperfil = :fotoperfil WHERE idalumno = :id";
                $stmtUpdate = $this->bd->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':fotoperfil' => $nombreFoto,
                    ':id'         => $alumno->getIdAlumno()
                ]);
                $alumno->setFotoPerfil($nombreFoto);
            }

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function borrar($idAlumno) {
        $alumno = $this->leer($idAlumno);
        if (!$alumno) {
            return false;
        }

        try {
            $this->bd->beginTransaction();

            // Borrar alumno
            $sql = "DELETE FROM alumnos WHERE idalumno = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idAlumno]);

            // Borrar usuario asociado
            $this->repoUsuarios->borrar($alumno->getIdUsuario());

            // Borrar archivo foto si existe
            if ($alumno->getFotoPerfil()) {
                $rutaFoto = __DIR__ . '/../../Public/Assets/Images/' . $alumno->getFotoPerfil();
                if (file_exists($rutaFoto)) {
                    unlink($rutaFoto);
                }
            }

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function listar() {
        $sql = "SELECT * FROM alumnos";
        $stmt = $this->bd->query($sql);
        $alumnos = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $alumnos[] = new Alumno(
                $fila['idalumno'],
                $fila['iduser'],
                null,
                $fila['nombre'],
                $fila['apellido1'],
                $fila['apellido2'],
                $fila['direccion'],
                $fila['edad'],
                $fila['curriculumurl'],
                $fila['fotoperfil']
            );
        }
        return $alumnos;
    }

    public function findById(int $idAlumno) {
        $sql = "SELECT * FROM alumnos WHERE idalumno = ?";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([$idAlumno]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$fila) {
            return null;
        }

        return new Alumno(
            $fila['idalumno'],
            $fila['iduser'],
            null,
            $fila['nombre'],
            $fila['apellido1'],
            $fila['apellido2'],
            $fila['direccion'],
            $fila['edad'],
            $fila['curriculumurl'],
            $fila['fotoperfil']
        );
    }

}
