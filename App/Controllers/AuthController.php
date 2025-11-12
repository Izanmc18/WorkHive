<?php 
namespace App\Controllers;
use League\Plates\Engine;

class AuthController
{

    public function renderLogin($engine)
    {
        echo $engine->render('Pages/Auth/Login');
    }

    public function renderRegRedirect($engine)
    {
        echo $engine->render('Pages/Auth/RegRedirect');
    }

    public function renderRegAlumno($engine)
    {
        echo $engine->render('Pages/Auth/RegisterAlumno');
    }

    public function renderRegEmpresa($engine)
    {
        echo $engine->render('Pages/Auth/RegisterEmpresa');
    }
}
