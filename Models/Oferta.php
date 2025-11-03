<?php
class Oferta {
    private $idOferta;
    private $idEmpresa;
    private $descripcion;
    private $fechaInicio;
    private $fechaFin;
    private $activa;
    private $titulo;

    public function __construct(
        $idOferta = null, $idEmpresa = null, $descripcion = null, $fechaInicio = null,
        $fechaFin = null, $activa = null, $titulo = null
    ) {
        $this->idOferta = $idOferta;
        $this->idEmpresa = $idEmpresa;
        $this->descripcion = $descripcion;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->activa = $activa;
        $this->titulo = $titulo;
    }

    public function getIdOferta() { 
        return $this->idOferta; 
    }
    public function setIdOferta($idOferta) { 
        $this->idOferta = $idOferta; 
    }

    public function getIdEmpresa() { 
        return $this->idEmpresa; 
    }

    public function setIdEmpresa($idEmpresa) { 
        $this->idEmpresa = $idEmpresa; 
    }

    public function getDescripcion() { 
        return $this->descripcion; 
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion; 
    }

    public function getFechaInicio() { 
        return $this->fechaInicio; 
    }

    public function setFechaInicio($fechaInicio) { 
        $this->fechaInicio = $fechaInicio; 
    }

    public function getFechaFin() { 
        return $this->fechaFin; 
    }

    public function setFechaFin($fechaFin) { 
        $this->fechaFin = $fechaFin; 
    }

    public function getActiva() { 
        return $this->activa; 
    }

    public function setActiva($activa) { 
        $this->activa = $activa; 
    }

    public function getTitulo() { 
        return $this->titulo; 
    }

    public function setTitulo($titulo) { 
        $this->titulo = $titulo;
    }
}
