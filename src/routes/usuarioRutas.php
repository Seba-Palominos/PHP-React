<?php

use Slim\App;
use App\Controllers\UserController;

return function(App $app) { 
    $app->post('/login', UserController::class . ':login');
    $app->post('/registro', UserController::class . ':registro');
    $app->put('/usuarios&/{usuario}',UserController::class. ':actualizar');
    $app->get('/usuario/{usuario}',UserController::class. ':traerDatos');
};