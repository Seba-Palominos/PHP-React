<?php
namespace app\middlewares;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class Alfanumerico implements MiddlewareInterface{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $datos = $request->getParsedBody();
        //Strlen nos ayuda a contar la cantidad de caracteres que tiene el string
        $cantidadChar = strlen($datos['contraseña']);
        if($cantidadChar < 6 || $cantidadChar > 20){
            if($cantidadChar < 6 ){
                $response = new Response();
                $response->getBody()->write(json_encode(['error:'=>'faltan caracteres']));
                $response = $response-> withHeader('content-type','application-json');
            }else{
                if ($cantidadChar > 20){
                    $response = new Response();
                    $response->getBody()->write(json_encode(['error:'=>'se pasa de 20 caracteres']));
                    $response = $response-> withHeader('content-type','application-json');
                }
            }
        return $response;
        }
        if (preg_match('/[A-Z]/',$datos['contraseña']) && preg_match('/[0-9]/',$datos['contraseña']) && preg_match('/\W/',$datos['contraseña'])){
            return $handler->handle($request);
        }
        $response = new Response();
        $response->getBody()->write(json_encode(['error'=> 'falta mayuscula,numero o caracter especial']));
        $response = $response->withHeader('content-type','application-json');
        return $response; 

    }
}
?>