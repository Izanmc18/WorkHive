<?php 
namespace App\Controllers;
use League\Plates\Engine;


class AdminController
{
    public function renderDashboard($engine) {
        echo $engine->render('Pages/Admin/DashboardAdmin');
    }
}

?>