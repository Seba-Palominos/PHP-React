<?php
use Slim\App;
use App\Controllers\UserControllers;
    return function(App $app){ 
    $app->post('/login','\app\controllers\UserController:login');

    $app->POST('registro','\app\controllers\Usercontrollers:registro');
    }

?>