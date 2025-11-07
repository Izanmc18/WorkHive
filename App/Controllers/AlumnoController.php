<?php 
namespace App\Controllers;
use League\Plates\Engine;
use App\Repositories\AlumnoRepo;
use App\Models\Alumno;

class AlumnoController
{

    public function renderList($engine)
    {
        echo $engine->render('Pages/ListadoAlumnos');
    }

    public function renderDashboard($engine) {}
}

?>
