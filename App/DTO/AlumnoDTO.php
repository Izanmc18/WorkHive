<?php
namespace App\DTO;

class AlumnoDTO
{
    private $idalumno;
    private $iduser;
    private $correo; 
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $direccion;
    private $edad;
    private $curriculumurl;
    private $fotoperfil;

    public function __construct($idalumno, $iduser, $correo, $nombre, $apellido1, $apellido2, $direccion, $edad, $curriculumurl, $fotoperfil) // 🛑 AÑADIR CORREO AL CONSTRUCTOR
    {
        $this->idalumno = $idalumno;
        $this->iduser = $iduser;
        $this->correo = $correo; 
        $this->nombre = $nombre;
        $this->apellido1 = $apellido1;
        $this->apellido2 = $apellido2;
        $this->direccion = $direccion;
        $this->edad = $edad;
        $this->curriculumurl = $curriculumurl;
        $this->fotoperfil = $fotoperfil;
    }

    // Getters Explícitos 

    public function getIdAlumno()
    {
        return $this->idalumno;
    }

    public function getIdUsuario()
    {
        return $this->iduser;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido1()
    {
        return $this->apellido1;
    }

    public function getApellido2()
    {
        return $this->apellido2;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getEdad()
    {
        return $this->edad;
    }

    public function getCurriculumUrl()
    {
        return $this->curriculumurl;
    }

    public function getFotoPerfil()
    {
        return $this->fotoperfil;
    }

    public function getCorreo() 
    {
        return $this->correo;
    }

    // Setters

    public function setIdAlumno($idalumno)
    {
        $this->idalumno = $idalumno;
    }

    public function setIdUsuario($iduser)
    {
        $this->iduser = $iduser;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido1($apellido1)
    {
        $this->apellido1 = $apellido1;
    }

    public function setApellido2($apellido2)
    {
        $this->apellido2 = $apellido2;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function setEdad($edad)
    {
        $this->edad = $edad;
    }

    public function setCurriculumUrl($curriculumurl)
    {
        $this->curriculumurl = $curriculumurl;
    }

    public function setFotoPerfil($fotoperfil)
    {
        $this->fotoperfil = $fotoperfil;
    }

    public function setCorreo($correo) 
    {
        $this->correo = $correo;
    }
    
    public function getPropiedad($propiedad)
    {
        
        if (property_exists($this, $propiedad)) {
            return $this->$propiedad;
        }
        
        $propiedadLower = strtolower($propiedad);
        if (property_exists($this, $propiedadLower)) {
            return $this->$propiedadLower;
        }
        return null;
    }

    public function setPropiedad($propiedad, $valor)
    {
        if (property_exists($this, $propiedad)) {
            $this->$propiedad = $valor;
        }
    }
}