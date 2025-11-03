<?php
class Usuario {
    private $id;
    private $correo;
    private $clave;

    public function __construct($id = null, $correo = null, $clave = null) {
        $this->id = $id;
        $this->correo = $correo;
        $this->clave = $clave;
    }

    public function getId() { 
        return $this->id; 
    }
    
    public function setId($id) {
        $this->id = $id; 
    }

    public function getCorreo() {
        return $this->correo; 
    }

    public function setCorreo($correo) {
        $this->correo = $correo; 
    }

    public function getClave() {
        return $this->clave; 
    }
    public function setClave($clave) {
        $this->clave = $clave; 
    }
}

