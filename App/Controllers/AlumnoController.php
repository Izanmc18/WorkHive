<?php 
namespace App\Controllers;
use League\Plates\Engine;

class AlumnoController {
    private $engine;

    public function __construct($engine = null) {
        $this->engine = new Engine(__DIR__ . '/../../Templates/AlumnoTemplates');
    }

    public function renderList() {
        echo $this->engine->render('AlumnoLista');
    }

    public function renderPerfilAlumno() {
        echo $this->engine->render('PerfilAlumno');
    }

    // Añade más métodos según tus necesidades
}

?>
