<?php

namespace App\Controllers;

use App\Repositories\RepositorioEmpresas;
use App\Helpers\Adapter;

class EmpresaController
{
    private $repositorio;

    public function __construct()
    {
        $this->repositorio = RepositorioEmpresas::getInstancia();
    }

    public function mostrarListado($engine)
    {
        $rutaSelf = "Location: " . $_SERVER['PHP_SELF'] . "?menu=admin-empresas";

        // Detectar qué botón fue presionado
        $accion = null;
        $botones = [
            'btnAñadirEmpresa',
            'btnGuardarEmpresa',
            'btnEditarEmpresa',
            'btnVerFichaEmpresa',
            'btnAceptarCambios',
            'btnCancelar',
            'btnEliminarEmpresa',
            'btnRechazarEmpresa',
            'btnConfirmarEmpresa'
        ];

        foreach ($botones as $boton) {
            if (isset($_POST[$boton])) {
                $accion = $boton;
                break;
            }
        }

        switch ($accion) {
            case 'btnAñadirEmpresa':
                echo $engine->render('Empresa/AñadirEmpresa');
                break;

            case 'btnGuardarEmpresa':
                $empresa = Adapter::dtoAEmpresa();
                $this->repositorio->crearEmpresa($empresa, null, null);
                header($rutaSelf);
                exit();

            case 'btnEditarEmpresa':
                $id = $_POST['btnEditarEmpresa'];
                $empresaDTO = Adapter::empresaADTO($id);
                echo $engine->render('Empresa/EditarEmpresa', [
                    'empresaEdit' => $empresaDTO
                ]);
                exit();

            case 'btnVerFichaEmpresa':
                $id = $_POST['btnVerFichaEmpresa'];
                $empresa = $this->repositorio->findById($id);
                echo $engine->render('Empresa/VerFichaEmpresa', [
                    'empresaVer' => $empresa
                ]);
                exit();

            case 'btnAceptarCambios':
                $id = $_POST['id'];
                Adapter::editarEmpresa($id, $_POST);
                header($rutaSelf);
                exit();

            case 'btnCancelar':
                header($rutaSelf);
                exit();

            case 'btnEliminarEmpresa':
                $this->eliminarEmpresa($_POST['btnEliminarEmpresa']);
                header($rutaSelf);
                exit();

            case 'btnRechazarEmpresa':
                $this->eliminarEmpresa($_POST['btnRechazarEmpresa']);
                header($rutaSelf);
                exit();

            case 'btnConfirmarEmpresa':
                $this->validarEmpresa($_POST['btnConfirmarEmpresa']);
                header($rutaSelf);
                exit();

            default:
                $empresasTodas = $this->repositorio->listar();
                $empresasTodasDTO = Adapter::todasEmpresasADTO($empresasTodas);
                $empresasLista = $empresasTodasDTO;
                $empresasPendientes = [];

                if (!empty($_POST['buscar'])) {
                    $filtro = $_POST['buscar'];
                    $empresasFiltradas = $this->repositorio->buscarPorNombre($filtro);
                    if (!empty($empresasFiltradas)) {
                        $empresasLista = Adapter::todasEmpresasADTO($empresasFiltradas);
                    }
                }
                /** PREGUNTAR A MANGEL PORQUE TIENE EMPRESAS PENDIENTES DE VALIDAR Y DEMAS */
                /** PREGUNTAR A MANGEL PORQUE TIENE EMPRESAS PENDIENTES DE VALIDAR Y DEMAS */
                /** PREGUNTAR A MANGEL PORQUE TIENE EMPRESAS PENDIENTES DE VALIDAR Y DEMAS */
                /**preguntar joder y no olvidarme*/
                foreach ($empresasTodasDTO as $empresa) {
                    //if (!$empresa->validacion) {
                    //    $empresasPendientes[] = $empresa;
                    //}
                }

                echo $engine->render('Empresa/ListadoEmpresas', [
                    'empresasTotal' => $empresasLista,
                    'pendientes' => $empresasPendientes
                ]);
                break;
        }
    }

    public function validarEmpresa($id)
    {
        $empresa = $this->repositorio->findById($id);
        $empresa->validacion = 1;
        return $this->repositorio->editarEmpresa($empresa, null, null);
    }

    public function eliminarEmpresa($id)
    {
        return $this->repositorio->borraridEmpresa($this->repositorio->findById($id));
    }
}
