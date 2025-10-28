<?php
require_once 'ConexionBD.php';

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

    // Crear un alumno con foto de perfil y apellidos
    public function crear($idUsuario, $nombre, $apellido1, $apellido2, $direccion, $edad, $curriculumUrl, $fotoPerfil) {
        $sql = "INSERT INTO alumnos 
        (id_user, nombre, apellido1, apellido2, direccion, edad, curriculum_url, foto_perfil)
        VALUES 
        (:idUsuario, :nombre, :apellido1, :apellido2, :direccion, :edad, :curriculumUrl, :fotoPerfil)";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute([
            'idUsuario' => $idUsuario,
            'nombre' => $nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'direccion' => $direccion,
            'edad' => $edad,
            'curriculumUrl' => $curriculumUrl,
            'fotoPerfil' => $fotoPerfil
        ]);
    }

    public function leer($idAlumno) {
        $sql = "SELECT * FROM alumnos WHERE id_alumno = :idAlumno";
        $consulta = $this->bd->prepare($sql);
        $consulta->execute(['idAlumno' => $idAlumno]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function modificarDireccion($idAlumno, $nuevaDireccion) {
        $sql = "UPDATE alumnos SET direccion = :nuevaDireccion WHERE id_alumno = :idAlumno";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['nuevaDireccion' => $nuevaDireccion, 'idAlumno' => $idAlumno]);
    }

    public function modificarFotoPerfil($idAlumno, $nuevaFoto) {
        $sql = "UPDATE alumnos SET foto_perfil = :nuevaFoto WHERE id_alumno = :idAlumno";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['nuevaFoto' => $nuevaFoto, 'idAlumno' => $idAlumno]);
    }

    public function modificarApellidos($idAlumno, $apellido1, $apellido2) {
        $sql = "UPDATE alumnos SET apellido1 = :apellido1, apellido2 = :apellido2 WHERE id_alumno = :idAlumno";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['apellido1' => $apellido1, 'apellido2' => $apellido2, 'idAlumno' => $idAlumno]);
    }

    public function borrar($idAlumno) {
        $sql = "DELETE FROM alumnos WHERE id_alumno = :idAlumno";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['idAlumno' => $idAlumno]);
    }
}
?>

