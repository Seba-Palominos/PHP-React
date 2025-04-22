<?php
    namespace app\services;
    use Psr\Http\Message\RequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    class Respuesta{
        public static function respuesta(Response $response,array $msj,int $status){
            $response->getBody()->write(json_encode($msj));
            $response = $response->withHeader("Content-Type","application/json")->withStatus($status);
            return $response;
    }
    }
?>