<?php 
namespace App\Controllers;
use League\Plates\Engine;

class LandingController {
    private $engine;

    public function __construct($engine = null) {
        //$this->engine = new Engine(__DIR__ . '/../../Templates/LandingTemplates');
        $this->engine = new \League\Plates\Engine(__DIR__ . '/../Templates');
    }

    public function renderLanding() {
        echo $this->engine->render('LandingTemplates::Landing');
    }

    public function renderSobreNosotros() {
        echo $this->engine->render('sobreNosotros');
    }
}

?>
