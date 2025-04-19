<?php
    use App\controllers\MazoController;
    use App\middlewares\ValidarIDsCartas;
    use Slim\App;
    return function (App $app) {
    $app->post('/mazos',MazoController::class .':recibirCartas')->add(ValidarIDsCartas::class);

    }
?>