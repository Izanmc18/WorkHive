<?php 
namespace App\Controllers;
use League\Plates\Engine;

class AuthController
{

    public function renderLogin($engine)
    {
        echo $engine->render('Pages/Login');
    }

    public function renderRegRedirect($engine)
    {
        echo $engine->render('Pages/RegRedirect');
    }

    public function renderRegAlumno($engine)
    {
        echo $engine->render('Pages/RegisterAlumno');
    }

    public function renderRegEmpresa($engine)
    {
        echo $engine->render('Pages/RegisterEmpresa');
    }
}
