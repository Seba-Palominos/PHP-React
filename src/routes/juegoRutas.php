<?php
use Slim\App;
use App\middlewares\ValidacionToken;
use App\controllers\JuegoController;
use App\middlewares\CamposVacios;
use App\middlewares\MazoPertenece;
return function(App $app){
$app->post('/partidas', JuegoController::class .':pertenece')
->add(ValidacionToken::class)
->add(CamposVacios::class)
->add(MazoPertenece::class);

$app->post('/jugadas',JuegoController::class . ':jugada');

$app->get('/usuarario/{usuario}/partidas/{partida}',JuegoController::class .':cartasEnMano')->add(ValidacionToken::class);
}
?>