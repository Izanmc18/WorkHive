<?php 
namespace App\Controllers;

class LandingController
{

    public function renderLanding($engine)
    {
        echo $engine->render('Pages/Landing');
    }
}

?>
