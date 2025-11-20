<?php 
namespace App\Controllers;
use League\Plates\Engine;
use App\Repositories\RepositorioAlumnos;
use App\Helpers\Adapter;
use App\Models\Alumno;

class AlumnoController
{

    // public function renderList($engine)
    // {

    //     $repoAlumnos = RepositorioAlumnos::getInstancia();
        
    //     $alumnosModelo = $repoAlumnos->listar();
        
    //     $alumnosDTO = Adapter::todosAlumnoADTO($alumnosModelo);
        
        
    //     echo $engine->render('Pages/Alumno/ListadoAlumnos', [
    //         'alumnos' => $alumnosDTO
    //     ]);
    // }

    public function renderDashboard($engine) {
        echo $engine->render('Pages/Alumno/DashboardAlumno');
    }
}