<?php

namespace App\Controllers;

use League\Plates\Engine;
use App\Repositories\RepositorioAlumnos;
// Si tienes estos repositorios, úsalos. Si no, deja los arrays vacíos abajo.
use App\Repositories\RepositorioOfertas; 
use App\Repositories\RepositorioSolicitudes;
use App\Helpers\Sesion;

class AlumnoController
{
    public function renderDashboard(Engine $engine)
    {
        // --- CORRECCIÓN AQUÍ ---
        // Usamos el método específico que tienes en tu Sesion.php
        $idUser = Sesion::obtenerIdUsuario(); 
        // -----------------------

        $repoAlumnos = RepositorioAlumnos::getInstancia();
        
        // Buscamos al alumno asociado a ese ID de usuario
        $alumno = $repoAlumnos->obtenerPorIdUsuario($idUser);

        // Inicializamos variables por defecto para evitar errores en la vista
        $ofertasDestacadas = [];
        $ultimasCandidaturas = [];
        $totalCandidaturas = 0;
        $ofertasDisponibles = 0;
        $perfilCompleto = false;

        if ($alumno) {
            // Verificar si el perfil está completo
            $perfilCompleto = !empty($alumno->getCurriculumUrl()) && !empty($alumno->getFotoPerfil());
            
            /* AQUÍ PUEDES CONECTAR TUS OTROS REPOSITORIOS CUANDO LOS TENGAS LISTOS:
               
               // Ejemplo:
               // $ultimasCandidaturas = RepositorioSolicitudes::getInstancia()->obtenerPorAlumno($alumno->getIdAlumno());
               // $totalCandidaturas = count($ultimasCandidaturas);
            */
        }

        echo $engine->render('Pages/Alumno/DashboardAlumno', [
            'alumno' => $alumno,
            'ofertasDestacadas' => $ofertasDestacadas,
            'ultimasCandidaturas' => $ultimasCandidaturas,
            'totalCandidaturas' => $totalCandidaturas,
            'ofertasDisponibles' => $ofertasDisponibles,
            'perfilCompleto' => $perfilCompleto
        ]);
    }

    public function renderListAlumnos(Engine $engine)
    {
        $repo = RepositorioAlumnos::getInstancia();
        $alumnos = $repo->listar();
        echo $engine->render('Pages/Alumno/ListadoAlumnos', [
            'alumnos' => $alumnos
        ]);
    }

public function verOfertas(Engine $engine)
    {
        
        $repoOfertas = RepositorioOfertas::getInstancia();
        $ofertas = $repoOfertas->listar(); 

        echo $engine->render('Pages/Alumno/OfertasAlumno', [
            'ofertas' => $ofertas
        ]);
    }

    public function verDetalleOferta(Engine $engine)
    {
        $idOferta = $_GET['id'] ?? null;
        if (!$idOferta) {
            header('Location: index.php?menu=alumno-ofertas');
            return;
        }

        $idUser = Sesion::obtenerIdUsuario();
        $repoAlumnos = RepositorioAlumnos::getInstancia();
        $alumno = $repoAlumnos->obtenerPorIdUsuario($idUser);

        $repoOfertas = RepositorioOfertas::getInstancia();
        $oferta = $repoOfertas->leer($idOferta);

        if (!$oferta) {
            echo "Oferta no encontrada.";
            return;
        }

        
        $repoSolicitudes = RepositorioSolicitudes::getInstancia();
        
        $yaInscrito = $repoSolicitudes->existeSolicitud($idOferta, $alumno->getIdAlumno());

        echo $engine->render('Pages/Alumno/VerOferta', [
            'oferta' => $oferta,
            'alumno' => $alumno,
            'yaInscrito' => $yaInscrito
        ]);
    }

    public function procesarPostulacion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idOferta = $_POST['id_oferta'] ?? null;
            $idAlumno = $_POST['id_alumno'] ?? null;

            if ($idOferta && $idAlumno) {
                $repoSolicitudes = RepositorioSolicitudes::getInstancia();
                
                $repoSolicitudes->crear($idOferta, $idAlumno); 
                
                
                header("Location: index.php?menu=alumno-ver-oferta&id=$idOferta&status=success");
            } else {
                header("Location: index.php?menu=alumno-ofertas&status=error");
            }
        }
    }

}