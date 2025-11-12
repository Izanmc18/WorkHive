<?php
namespace App\DTO;

class CicloDTO
{
    private $idciclo;
    private $nombre;
    private $tipo;
    private $idfamilia;

    public function __construct($idciclo, $nombre, $tipo, $idfamilia)
    {
        $this->idciclo = $idciclo;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->idfamilia = $idfamilia;
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
