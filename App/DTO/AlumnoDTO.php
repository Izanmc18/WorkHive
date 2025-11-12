<?php
namespace App\DTO;

class AlumnoDTO
{
    private $idalumno;
    private $iduser;
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $direccion;
    private $edad;
    private $curriculumurl;
    private $fotoperfil;

    public function __construct($idalumno, $iduser, $nombre, $apellido1, $apellido2, $direccion, $edad, $curriculumurl, $fotoperfil)
    {
        $this->idalumno = $idalumno;
        $this->iduser = $iduser;
        $this->nombre = $nombre;
        $this->apellido1 = $apellido1;
        $this->apellido2 = $apellido2;
        $this->direccion = $direccion;
        $this->edad = $edad;
        $this->curriculumurl = $curriculumurl;
        $this->fotoperfil = $fotoperfil;
    }

    public function getPropiedad($propiedad)
    {
        if (property_exists($this, $propiedad)) {
            return $this->$propiedad;
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
