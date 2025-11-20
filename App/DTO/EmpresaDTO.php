<?php
namespace App\DTO;

class EmpresaDTO
{
    private $idempresa;
    private $iduser;
    private $nombre;
    private $descripcion;
    private $logourl;
    private $direccion;
    private $validacion;

    public function __construct($idempresa, $iduser, $nombre, $descripcion, $logourl, $direccion, $validacion)
    {
        $this->idempresa = $idempresa;
        $this->iduser = $iduser;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->logourl = $logourl;
        $this->direccion = $direccion;
        $this->validacion = $validacion;
    }

    // Getters
    public function getIdempresa()
    {
        return $this->idempresa;
    }

    public function getIduser()
    {
        return $this->iduser;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getLogourl()
    {
        return $this->logourl;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getValidacion()
    {
        return $this->validacion;
    }

    // Setters
    public function setIdempresa($idempresa)
    {
        $this->idempresa = $idempresa;
    }

    public function setIduser($iduser)
    {
        $this->iduser = $iduser;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function setLogourl($logourl)
    {
        $this->logourl = $logourl;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function setValidacion($validacion)
    {
        $this->validacion = $validacion;
    }
}
