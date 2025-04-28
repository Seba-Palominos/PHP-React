<?php
use Slim\App;
use App\middlewares\ValidacionToken;
use App\controllers\juegoController;
use App\middlewares\UsuarioMiddleware;
$app->post('/partidas', juegoController::class .':pertenece')->add(ValidacionToken::class)->add(UsuarioMiddleware::class);
?>