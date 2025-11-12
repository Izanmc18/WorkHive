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

    public function __construct($idempresa, $iduser, $nombre, $descripcion, $logourl, $direccion)
    {
        $this->idempresa = $idempresa;
        $this->iduser = $iduser;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->logourl = $logourl;
        $this->direccion = $direccion;
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
