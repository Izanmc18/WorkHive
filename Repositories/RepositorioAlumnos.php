<?php
require_once 'ConexionBD.php';
require_once '../Helpers/Security/PasswordSecurity.php';
require_once 'RepositorioUsuarios.php';

class RepositorioAlumnos {
    private static $instancia = null;
    private $bd;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioAlumnos();
        }
        return self::$instancia;
    }

    public function crear($correo, $clave, $nombre, $apellido1, $apellido2, $direccion, $edad, $curriculumUrl, $fotoPerfil) {
        try {
            $this->bd->beginTransaction();

            $repoUsuarios = RepositorioUsuarios::getInstancia();
            $idUsuario = $repoUsuarios->crear($correo, $clave);

            $sql = "INSERT INTO alumnos (id_user, nombre, apellido1, apellido2, direccion, edad, curriculum_url, foto_perfil)
                    VALUES (:idUsuario, :nombre, :apellido1, :apellido2, :direccion, :edad, :curriculumUrl, :fotoPerfil)";
            $consulta = $this->bd->prepare($sql);

            $consulta->execute([
                'idUsuario' => $idUsuario,
                'nombre' => $nombre,
                'apellido1' => $apellido1,
                'apellido2' => $apellido2,
                'direccion' => $direccion,
                'edad' => $edad,
                'curriculumUrl' => $curriculumUrl,
                'fotoPerfil' => $fotoPerfil
            ]);

            $this->bd->commit();
            return true;
        } catch (Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function borrar($idAlumno) {
        try {
            $this->bd->beginTransaction();

            $alumno = $this->leer($idAlumno);
            if (!$alumno) {
                $this->bd->rollBack();
                return false;
            }

            $sql = "DELETE FROM alumnos WHERE id_alumno = :idAlumno";
            $consulta = $this->bd->prepare($sql);
            $consulta->execute(['idAlumno' => $idAlumno]);

            $repoUsuarios = RepositorioUsuarios::getInstancia();
            $repoUsuarios->borrar($alumno['id_user']);

            $this->bd->commit();
            return true;
        } catch (Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function leer($idAlumno) {
        $sql = "SELECT * FROM alumnos WHERE id_alumno = :idAlumno";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['idAlumno' => $idAlumno]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function editar($idAlumno, $nombre, $apellido1, $apellido2, $direccion, $edad, $curriculumUrl, $fotoPerfil) {
        try {
            $this->bd->beginTransaction();

            $sql = "UPDATE alumnos
                    SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2,
                        direccion = :direccion, edad = :edad, curriculum_url = :curriculumUrl,
                        foto_perfil = :fotoPerfil 
                    WHERE id_alumno = :idAlumno";
            $consulta = $this->bd->prepare($sql);
            $consulta->execute([
                'nombre' => $nombre,
                'apellido1' => $apellido1,
                'apellido2' => $apellido2,
                'direccion' => $direccion,
                'edad' => $edad,
                'curriculumUrl' => $curriculumUrl,
                'fotoPerfil' => $fotoPerfil,
                'idAlumno' => $idAlumno
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