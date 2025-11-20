<?php 
namespace App\Controllers;
use League\Plates\Engine;
use App\Repositories\RepositorioAlumnos;
use App\Helpers\Adapter;

class AdminController
{
    public function renderDashboard($engine) {
        echo $engine->render('Pages/Admin/DashboardAdmin');
    }

    public function renderListAlumnos($engine)
    {

        $repoAlumnos = RepositorioAlumnos::getInstancia();
        
        $alumnosModelo = $repoAlumnos->listar();
        
        $alumnosDTO = Adapter::todosAlumnoADTO($alumnosModelo);
        
        
        echo $engine->render('Pages/Alumno/ListadoAlumnos', [
            'alumnos' => $alumnosDTO
        ]);
    }
}

?>