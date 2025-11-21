<?php 
namespace App\Controllers;
use League\Plates\Engine;
use App\Repositories\RepositorioAlumnos;
use App\Helpers\Adapter;
use App\Models\Alumno;

class AlumnoController
{


    public function renderDashboard($engine) {
        echo $engine->render('Pages/Alumno/DashboardAlumno');
    }
}