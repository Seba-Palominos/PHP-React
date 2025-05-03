<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\App;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

(require __DIR__. '/src/routes/usuarioRutas.php')($app) ;
(require __DIR__. '/src/routes/MazoRutas.php')($app);
(require __DIR__. '/src/routes/juegoRutas.php')($app);
$app->run();