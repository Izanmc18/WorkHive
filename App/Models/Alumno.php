<?php
namespace App\Models;

class Alumno {
    private $idAlumno;
    private $idUsuario;
    private $correo;
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $direccion;
    private $edad;
    private $curriculumUrl;
    private $fotoPerfil;

    public function __construct(
        $idAlumno = null, $idUsuario = null, $correo = null, $nombre = null,
        $apellido1 = null, $apellido2 = null, $direccion = null, $edad = null,
        $curriculumUrl = null, $fotoPerfil = null
    ) {
        $this->idAlumno = $idAlumno;
        $this->idUsuario = $idUsuario;
        $this->correo = $correo;
        $this->nombre = $nombre;
        $this->apellido1 = $apellido1;
        $this->apellido2 = $apellido2;
        $this->direccion = $direccion;
        $this->edad = $edad;
        $this->curriculumUrl = $curriculumUrl;
        $this->fotoPerfil = $fotoPerfil;
    }

    public function getIdAlumno() { 
        return $this->idAlumno; 
    }
    public function setIdAlumno($idAlumno) { 
        $this->idAlumno = $idAlumno; 
    }

    public function getIdUsuario() { 
        return $this->idUsuario; 
    }

    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario; 
    }

    public function getCorreo() { 
        return $this->correo; 
    }

    public function setCorreo($correo) { 
        $this->correo = $correo; 
    }

    public function getNombre() { 
        return $this->nombre; 
    }
    public function setNombre($nombre) { 
        $this->nombre = $nombre; 
    }

    public function getApellido1() { 
        return $this->apellido1; 
    }

    public function setApellido1($apellido1) { 
        $this->apellido1 = $apellido1; 
    }

    public function getApellido2() { 
        return $this->apellido2; 
    }

    public function setApellido2($apellido2) { 
        $this->apellido2 = $apellido2; 
    }

    public function getDireccion() { 
        return $this->direccion; 
    }

    public function setDireccion($direccion) { 
        $this->direccion = $direccion; 
    }

    public function getEdad() { 
        return $this->edad; 
    }

    public function setEdad($edad) { 
        $this->edad = $edad; 
    }

    public function getCurriculumUrl() { 
        return $this->curriculumUrl; 
    }

    public function setCurriculumUrl($curriculumUrl) { 
        $this->curriculumUrl = $curriculumUrl; 
    }

    public function getFotoPerfil() { 
        return $this->fotoPerfil; 
    }
    public function setFotoPerfil($fotoPerfil) { 
        $this->fotoPerfil = $fotoPerfil; 
    }
}
