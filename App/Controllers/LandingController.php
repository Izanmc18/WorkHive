<?php 
namespace App\Controllers;
use App\Helpers\Sesion;
class LandingController
{

    public function renderLanding($engine)
    {

        $role = Sesion::obtenerRol() ?? 'ROLE_GUEST';

        echo $engine->render('Pages/Landing',[
                'title' => 'WorkHive - Pagina Principal',
                'role' => $role,
                'username' => Sesion::obtenerNombreUsuario()
        ]);
    }
}

?>
