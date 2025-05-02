<?php
use Slim\App;
use App\middlewares\ValidacionToken;
use App\controllers\juegoController;
use App\middlewares\UsuarioMiddleware;
use App\middlewares\MazoPertenece;
$app->post('/partidas', JuegoController::class .':pertenece')
->add(ValidacionToken::class)
->add(UsuarioMiddleware::class)
->add(MazoPertenece::class);

$app->post('/jugadas',JuegoController::class . ':jugada');
?>