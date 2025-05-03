<?php

use Slim\App;
use App\Controllers\UserController;
use App\middlewares\CamposVacios;
use App\middlewares\Alfanumerico;
use App\middlewares\ValidacionToken;
return function(App $app) { 
    $app->post('/login', UserController::class . ':login')->add(CamposVacios::class);
    $app->post('/registro', UserController::class . ':registro')->add(CamposVacios::class)->add(Alfanumerico::class);
    $app->put('/usuarios&/{usuario}',UserController::class. ':actualizar')->add(CamposVacios::class)
    ->add(Alfanumerico::class)
    ->add(ValidacionToken::class);
    $app->get('/usuario/{usuario}',UserController::class. ':traerDatos')->add(ValidacionToken::class);
};