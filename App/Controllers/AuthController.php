<?php 
namespace App\Controllers;

use League\Plates\Engine;
use App\Helpers\Security\Validator;
use App\Helpers\Security\Authorization;
use App\Repositories\RepositorioUsuarios;
use App\Repositories\RepositorioEmpresas;
use App\Repositories\RepositorioAlumnos;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Alumno;
use App\Helpers\Sesion;

class AuthController
{
    private $repositorioUsuarios;
    private $repositorioEmpresas;
    private $repositorioAlumnos;

    public function __construct()
    {
        $this->repositorioUsuarios = RepositorioUsuarios::getInstancia();
        $this->repositorioEmpresas = RepositorioEmpresas::getInstancia();
        $this->repositorioAlumnos = RepositorioAlumnos::getInstancia();
    }

    
    public function renderRegRedirect(Engine $engine)
    {
        echo $engine->render('Pages/Auth/RegRedirect');
    }

    public function renderRegEmpresa(Engine $engine)
    {
        echo $engine->render('Pages/Auth/RegisterEmpresa');
    }

    public function renderRegAlumno(Engine $engine)
    {
        echo $engine->render('Pages/Auth/RegisterAlumno');
    }

    public function renderLogin(Engine $engine)
    {
        echo $engine->render('Pages/Auth/Login');
    }
    
    // LÓGICA DE REGISTRO 

    /**
     * Procesa la creación de un nuevo registro de empresa.
     */
    public function procesarRegistroEmpresa(Engine $engine)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['correo'], $_POST['nombre'], $_POST['clave'])) {
            $_SESSION['error'] = 'Datos incompletos para el registro.';
            $this->renderRegEmpresa($engine);
            return;
        }
        
        $repoEmpresas = RepositorioEmpresas::getInstancia();

        $usuario = new Usuario(
            null,
            $_POST['correo'],
            $_POST['clave'],
            false, 
            false  
        );

        $empresa = new Empresa(
            null, 
            null, 
            $_POST['correo'], 
            $_POST['nombre'], 
            $_POST['descripcion'] ?? null,
            null, 
            $_POST['direccion'] ?? null
        );

        $archivoLogo = (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) 
            ? $_FILES['logo'] 
            : null;

        try {
            $repoEmpresas->crear($empresa, $usuario, $archivoLogo);

            $_SESSION['registro_exito'] = 'Registro exitoso. Tu cuenta está pendiente de validación, puedes iniciar sesión ahora.';
            header('Location: index.php?menu=login');
            exit;

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $_SESSION['error'] = 'El correo electrónico ya está registrado.';
            } else {
                $_SESSION['error'] = 'Error al registrar la empresa: ' . $e->getMessage();
                error_log("Error de registro de empresa: " . $e->getMessage());
            }
            $this->renderRegEmpresa($engine);
            return;
        }
    }
    
    /**
     * Procesa la creación de un nuevo registro de alumno.
     */
    public function procesarRegistroAlumno(Engine $engine)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['correo'], $_POST['nombre'], $_POST['contrasena'])) {
            $_SESSION['error'] = 'Datos incompletos para el registro.';
            $this->renderRegAlumno($engine);
            return;
        }
        
        $repoAlumnos = RepositorioAlumnos::getInstancia();

        $usuario = new Usuario(
            null,
            $_POST['correo'],
            $_POST['contrasena'],
            false, 
            false  
        );

        $alumno = new Alumno(
            null, 
            null, 
            $_POST['correo'], 
            $_POST['nombre'], 
            $_POST['apellido1'] ?? null, 
            $_POST['apellido2'] ?? null, 
            $_POST['direccion'] ?? null, 
            $_POST['edad'] ?? null,
            null, 
            null  
        );

        
        $archivoFoto = (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) 
            ? $_FILES['fotoPerfil'] 
            : null;
            
        $archivoCV = (isset($_FILES['curriculum']) && $_FILES['curriculum']['error'] === UPLOAD_ERR_OK) 
            ? $_FILES['curriculum'] 
            : null;

        try {
            $repoAlumnos->create($alumno, $usuario, $archivoFoto, $archivoCV);

            $_SESSION['registro_exito'] = 'Registro exitoso. Inicia sesión para completar tu perfil.';
            header('Location: index.php?menu=login');
            exit;

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $_SESSION['error'] = 'El correo electrónico ya está registrado.';
            } else {
                $_SESSION['error'] = 'Error al registrar el alumno: ' . $e->getMessage();
                error_log("Error de registro de alumno: " . $e->getMessage());
            }
            $this->renderRegAlumno($engine);
            return;
        }
    }

    /* LÓGICA DE LOGIN */
      
    public function procesarLogin(Engine $engine)
    {
        // Le decimos al navegador que la respuesta es JSON
        header('Content-Type: application/json');

        $correo = $_POST['correo'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';

        if (empty($correo) || empty($contrasena)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Debes completar todos los campos.']);
            exit;
        }
        
        $usuarioValido = Validator::validarUsuario($correo, $contrasena); 
        
        //var_dump($usuarioValido);

        if ($usuarioValido) {
            
            $rol = $this->getSpecificRole($usuarioValido);

            if ($rol === null) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Error de configuración de usuario. Contacte al administrador.']);
                exit;
            }

            $token = Authorization::generarToken($usuarioValido);
            Sesion::iniciar();
            
            Sesion::establecerSesion([
                'usuario' => $usuarioValido,
                'token' => $token,
                'rol' => $rol 
            ]);
            
            session_write_close(); 
            
            // 🎯 CAMBIO CLAVE: En lugar de redirigir, devolvemos la URL en un JSON
            $dashboardRoute = $this->getDashboardRouteFromRole($rol); 
            
            echo json_encode([
                'success' => true,
                'redirect' => "index.php?menu=$dashboardRoute" // Enviamos la URL destino
            ]);
            exit;

        } else {
            http_response_code(401); 
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
            exit; 
        }
    }

    // ... (El resto de funciones getSpecificRole, getDashboardRouteFromRole y logout se quedan igual) ...
    private function getSpecificRole(Usuario $usuario) : ?string
    {
        if ($usuario->isAdmin()) return 'admin';
        $idUsuario = $usuario->getId();
        if ($this->repositorioEmpresas->obtenerPorIdUsuario($idUsuario)) return 'empresa';
        if ($this->repositorioAlumnos->obtenerPorIdUsuario($idUsuario)) return 'alumno';
        return null;
    }

    private function getDashboardRouteFromRole(string $role) : string
    {
        switch ($role) {
            case 'alumno': return 'alumno-dashboard';
            case 'empresa': return 'empresa-dashboard';
            case 'admin': return 'admin-dashboard';
            default: return 'landing';
        }
    }

    public function logout()
    {
        $token = Sesion::obtenerToken();
        if ($token) Authorization::deleteToken($token);
        Sesion::cerrarSesion();
        header('Location: index.php?menu=login');
        exit;
    }

}
