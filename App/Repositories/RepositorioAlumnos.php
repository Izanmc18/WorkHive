<?php
namespace App\Repositories;

use App\Repositories\ConexionBD;
use App\Models\Alumno;
use App\Models\Usuario;
use App\Repositories\RepositorioUsuarios;


class RepositorioAlumnos {
    private $bd;
    private $repoUsuarios;

    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
        $this->repoUsuarios = new RepositorioUsuarios();
    }

    public function crear(Alumno $alumno, Usuario $usuario) {
        try {
            $this->bd->beginTransaction();

            $usuarioCreado = $this->repoUsuarios->crear($usuario);
            $alumno->setIdUsuario($usuarioCreado->getId());

            $sql = "INSERT INTO alumnos (iduser, nombre, apellido1, apellido2, direccion, edad, curriculumurl, fotoperfil) VALUES
                    (:iduser, :nombre, :apellido1, :apellido2, :direccion, :edad, :curriculumurl, :fotoperfil)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':iduser' => $alumno->getIdUsuario(),
                ':nombre' => $alumno->getNombre(),
                ':apellido1' => $alumno->getApellido1(),
                ':apellido2' => $alumno->getApellido2(),
                ':direccion' => $alumno->getDireccion(),
                ':edad' => $alumno->getEdad(),
                ':curriculumurl' => $alumno->getCurriculumUrl(),
                ':fotoperfil' => $alumno->getFotoPerfil()
            ]);

            $alumno->setIdAlumno($this->bd->lastInsertId());

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
        return new Alumno($fila['idalumno'], $fila['iduser'], null, $fila['nombre'], $fila['apellido1'], $fila['apellido2'], $fila['direccion'], $fila['edad'], $fila['curriculumurl'], $fila['fotoperfil']);
    }

    public function editar(Alumno $alumno) {
        $sql = "UPDATE alumnos SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2,
                direccion = :direccion, edad = :edad, curriculumurl = :curriculumurl, fotoperfil = :fotoperfil WHERE idalumno = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':nombre' => $alumno->getNombre(),
            ':apellido1' => $alumno->getApellido1(),
            ':apellido2' => $alumno->getApellido2(),
            ':direccion' => $alumno->getDireccion(),
            ':edad' => $alumno->getEdad(),
            ':curriculumurl' => $alumno->getCurriculumUrl(),
            ':fotoperfil' => $alumno->getFotoPerfil(),
            ':id' => $alumno->getIdAlumno()
        ]);
    }

    public function borrar($idAlumno) {
        $alumno = $this->leer($idAlumno);
        if (!$alumno) {
            return false;
        }
        try {
            $this->bd->beginTransaction();

            $sql = "DELETE FROM alumnos WHERE idalumno = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idAlumno]);

            $this->repoUsuarios->borrar($alumno->getIdUsuario());

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
            $alumnos[] = new Alumno($fila['idalumno'], $fila['iduser'], null, $fila['nombre'], $fila['apellido1'], $fila['apellido2'], $fila['direccion'], $fila['edad'], $fila['curriculumurl'], $fila['fotoperfil']);
        }
        return $alumnos;
    }
}
?>