<?php
    use App\controllers\MazoController;
    use App\middlewares\ValidarIDsCartas;
    use App\middlewares\UsuarioMiddleware;
    use App\middlewares\ValidacionToken;
    use App\middlewares\ValidacionMazos;
    use Slim\App;
    return function (App $app) {
    $app->post('/mazos',MazoController::class .':recibirCartas')
    ->add(ValidacionMazos::class)
    ->add(ValidarIDsCartas::class)
    ->add(UsuarioMiddleware::class)
    ->add(ValidacionToken::class);
    $app->delete('/mazosA/{mazo}',MazoController::class .':deleteMazo')
    ->add(ValidacionToken::class);
    $app->get('/usuarios/{usuario}/mazo',MazoController::class .':obtenerMazo');
    $app->put('/mazos/{mazo}',MazoController::class .'actualizarMazo');
    $app->get(' /cartas?atributo={atributo}&nombre={nombre}',MazoController::class .'cartas');    
    }
?>