<?php
class Empresa {
    private $idEmpresa;
    private $idUsuario;
    private $correo;
    private $nombre;
    private $descripcion;
    private $logoUrl;
    private $direccion;

    public function __construct(
        $idEmpresa = null, $idUsuario = null, $correo = null, $nombre = null,
        $descripcion = null, $logoUrl = null, $direccion = null
    ) {
        $this->idEmpresa = $idEmpresa;
        $this->idUsuario = $idUsuario;
        $this->correo = $correo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->logoUrl = $logoUrl;
        $this->direccion = $direccion;
    }

    public function getIdEmpresa() { 
        return $this->idEmpresa; 
    }

    public function setIdEmpresa($idEmpresa) { 
        $this->idEmpresa = $idEmpresa; 
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

    public function getDescripcion() { 
        return $this->descripcion; 
    }

    public function setDescripcion($descripcion) { 
        $this->descripcion = $descripcion; 
    }

    public function getLogoUrl() { 
        return $this->logoUrl; 
    }

    public function setLogoUrl($logoUrl) { 
        $this->logoUrl = $logoUrl; 
    }

    public function getDireccion() { 
        return $this->direccion; 
    }

    public function setDireccion($direccion) { 
        $this->direccion = $direccion; 
    }
}

