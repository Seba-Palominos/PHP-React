<?php

use Slim\App;
use App\Controllers\UserController;
use App\middlewares\UsuarioMiddleware;
use App\middlewares\Alfanumerico;
use App\middlewares\ValidacionToken;
return function(App $app) { 
    $app->post('/login', UserController::class . ':login')->add(UsuarioMiddleware::class);
    $app->post('/registro', UserController::class . ':registro')->add(UsuarioMiddleware::class)->add(Alfanumerico::class);
    $app->put('/usuarios&/{usuario}',UserController::class. ':actualizar')->add(UsuarioMiddleware::class)
    ->add(Alfanumerico::class)
    ->add(ValidacionToken::class);
    $app->get('/usuario/{usuario}',UserController::class. ':traerDatos');
};