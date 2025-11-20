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

    // --- Getters Explícitos ---

    public function getIdCiclo()
    {
        return $this->idciclo;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getIdFamilia()
    {
        return $this->idfamilia;
    }

    // --- Setters Explícitos ---

    public function setIdCiclo($idciclo)
    {
        $this->idciclo = $idciclo;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setIdFamilia($idfamilia)
    {
        $this->idfamilia = $idfamilia;
    }

    
}