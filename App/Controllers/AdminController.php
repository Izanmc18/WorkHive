<?php 
namespace App\Controllers;
use League\Plates\Engine;
use App\Repositories\RepositorioUsuarios;
use App\Repositories\RepositorioEmpresas;
use App\Repositories\RepositorioOfertas;
use App\Repositories\RepositorioAlumnos;
use App\Helpers\Adapter;

class AdminController
{
    public function renderDashboard(Engine $engine) {
        
        $repoUsuarios = RepositorioUsuarios::getInstancia();
        $repoEmpresas = RepositorioEmpresas::getInstancia();
        $repoOfertas = RepositorioOfertas::getInstancia();
        $repoAlumnos = RepositorioAlumnos::getInstancia();

        $stats = [
            'totalUsuarios' => $repoUsuarios->contarTotalUsuarios(),
            'totalEmpresas' => $repoEmpresas->contarTotalEmpresas(),
            'pendientesEmpresa' => $repoEmpresas->contarEmpresasPendientes(),
            'totalOfertas' => $repoOfertas->contarTotalOfertas(),
            'totalAlumnos' => $repoAlumnos->contarTotalAlumnos(),
            'totalGeneral' => $repoUsuarios->contarTotalUsuarios() + $repoEmpresas->contarTotalEmpresas() + $repoAlumnos->contarTotalAlumnos() 
        ];

        echo $engine->render('Pages/Admin/DashboardAdmin', $stats);
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