<?php
use Slim\App;
use App\controllers\juegoController;
$app->post('/partidas', juegoController::class .':pertenece');
?>