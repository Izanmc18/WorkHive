<?php

namespace App\Helpers;

use App\Models\Alumno;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Ciclo;
use App\DTO\AlumnoDTO;
use App\DTO\EmpresaDTO;
use App\DTO\CicloDTO;
use App\Repositories\RepositorioAlumnos;
use App\Repositories\RepositorioEmpresas;
use App\Repositories\RepositorioCiclos;

class Adapter
{
    public static function dtoAAlumno($data)
    {
        $alumno = new Alumno(
            null,
            null,
            $data['correo'],
            $data['nombre'],
            $data['apellido1'],
            $data['apellido2'],
            $data['direccion'],
            $data['edad'],
            $data['curriculumUrl'] ?? '', 
            $data['fotoPerfil'] ?? ''     
        );

        return $alumno;
    }

    public static function todosAlumnoADTO($alumnos)
    {
        $alumnosDTO = [];

        if (empty($alumnos)) {
            return $alumnosDTO;
        }

        foreach ($alumnos as $alumno) {
            $alumnosDTO[] = new AlumnoDTO(
                $alumno->getIdAlumno(),
                $alumno->getIdUsuario(),
                $alumno->getCorreo(), 
                $alumno->getNombre(),
                $alumno->getApellido1(),
                $alumno->getApellido2(),
                $alumno->getDireccion(),
                $alumno->getEdad(),
                $alumno->getCurriculumUrl(),
                $alumno->getFotoPerfil()
            );
        }

        return $alumnosDTO;
    }

    public static function alumnoADTO($id)
    {
        $repositorio = RepositorioAlumnos::getInstancia();
        $alumno = $repositorio->findById($id);

        if (!$alumno) {
            return null;
        }

        return new AlumnoDTO(
            $alumno->getIdAlumno(),
            $alumno->getIdUsuario(),
            $alumno->getCorreo(),
            $alumno->getNombre(),
            $alumno->getApellido1(),
            $alumno->getApellido2(),
            $alumno->getDireccion(),
            $alumno->getEdad(),
            $alumno->getCurriculumUrl(),
            $alumno->getFotoPerfil()
        );
    }

    public static function datosEditadosADTO($data)
    {
        return new AlumnoDTO(
            $data['idAlumno'],
            $data['idUsuario'],
            $data['correo'],
            $data['nombre'],
            $data['apellido1'],
            $data['apellido2'],
            $data['direccion'],
            $data['edad'],
            $data['curriculumUrl'] ?? '',
            $data['fotoPerfil'] ?? ''
        );
    }

    public static function grupoDTOAAlumno($alumnosDTO)
    {
        $alumnos = [];
        $repositorioCiclos = RepositorioCiclos::getInstancia();

        foreach ($alumnosDTO as $aluDTO) {
            
            $alumno = new Alumno(
                null,
                null,
                $aluDTO->getPropiedad('correo'),
                $aluDTO->getPropiedad('nombre'),
                $aluDTO->getPropiedad('apellido1'),
                $aluDTO->getPropiedad('apellido2'),
                $aluDTO->getPropiedad('direccion'),
                $aluDTO->getPropiedad('edad'),
                $aluDTO->getPropiedad('curriculumurl'),
                $aluDTO->getPropiedad('fotoperfil')
            );

            
            $idCiclo = $aluDTO->getPropiedad('idciclo');
            if ($idCiclo) {
                $ciclo = $repositorioCiclos->findById($idCiclo);
                if ($ciclo) {
                    $alumno->agregarEstudio($ciclo);
                }
            }

            $alumnos[] = $alumno;
        }

        return $alumnos;
    }

    
    public static function dtoAEmpresa($data)
    {
        return new Empresa(
            null,
            null,
            $data['correo'],
            $data['nombre'],
            $data['descripcion'],
            $data['logoUrl'] ?? '', 
            $data['direccion']
        );
    }

    public static function todasEmpresasADTO($empresas)
    {
        $empresasDTO = [];

        foreach ($empresas as $empresa) {
            $empresasDTO[] = new EmpresaDTO(
                $empresa->getIdEmpresa(),
                $empresa->getIdUsuario(),
                $empresa->getCorreo(),
                $empresa->getNombre(),
                $empresa->getDescripcion(),
                $empresa->getLogoUrl(),
                $empresa->getDireccion(),
                $empresa->getValidacion()
            );
        }

        return $empresasDTO;
    }

    public static function empresaADTO($id)
    {
        $repositorio = RepositorioEmpresas::getInstancia();
        $empresa = $repositorio->findById($id);

        if (!$empresa) {
            return null;
        }

        return new EmpresaDTO(
            $empresa->getIdEmpresa(),
            $empresa->getIdUsuario(),
            $empresa->getCorreo(),
            $empresa->getNombre(),
            $empresa->getDescripcion(),
            $empresa->getLogoUrl(),
            $empresa->getDireccion(),
            $empresa->getValidacion()
        );
    }

    public static function editarEmpresa($id, $postData)
    {
        $repositorio = RepositorioEmpresas::getInstancia();
        $empresa = $repositorio->findById($id);

        if (!$empresa) {
            return false;
        }

        $empresa->setCorreo($postData['correo']);
        $empresa->setNombre($postData['nombre']);
        $empresa->setDescripcion($postData['descripcion']);
        
        if (isset($postData['logoUrl'])) {
            $empresa->setLogoUrl($postData['logoUrl']);
        }
        $empresa->setDireccion($postData['direccion']);
        
        
        
        $usuario = new Usuario(
            $empresa->getIdUsuario(),
            $postData['correo'],
            '', 
            false,
            false
        );

        return $repositorio->editar($empresa, $usuario);
    }

    public static function ciclosADTO($ciclos)
    {
        $ciclosDTO = [];

        foreach ($ciclos as $ciclo) {
            $ciclosDTO[] = new CicloDTO(
                $ciclo->getIdCiclo(),
                $ciclo->getNombre(),
                $ciclo->getTipo(),
                $ciclo->getIdFamilia()
            );
        }

        return $ciclosDTO;
    }
}