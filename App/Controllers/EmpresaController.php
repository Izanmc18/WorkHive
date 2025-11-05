<?php 
namespace App\Controllers;
use League\Plates\Engine;

class EmpresaController {
    private $engine;

    public function __construct($engine = null) {
        $this->engine = new Engine(__DIR__ . '/../../Templates/EmpresaTemplates');
    }

    public function renderList() {
        echo $this->engine->render('empresa-lista');
    }

    public function renderPerfilEmpresa() {
        echo $this->engine->render('PerfilEmpresa');
    }

    public function renderOfertas() {
        echo $this->engine->render('Ofertas');
    }
    
    // Añade más métodos según tus necesidades
}

?>
