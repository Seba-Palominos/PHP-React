<?php
namespace app\middlewares;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class Alfanumerico implements MiddlewareInterface{
    public function respuesta(Response $response,Array $msj){
        $response->getBody()->write(json_encode($msj));
        $response = $response-> withHeader('content-type','application-json');
        return $response;
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $datos = $request->getParsedBody();
        //Strlen nos ayuda a contar la cantidad de caracteres que tiene el string
        $cantidadChar = strlen($datos['contraseña']);
        if($cantidadChar < 6 || $cantidadChar > 20){
            $response = new Response();
            if($cantidadChar < 6 ){
                return $this->respuesta($response,['error:'=>'faltan caracteres']);
            }else{
                if ($cantidadChar > 20){
                    return $this->respuesta($response,['error'=> 'se pasa de 20 caracteres']);
                }
            }
        }
        if (!preg_match('/[A-Z]/',$datos['contraseña']) && preg_match('/[0-9]/',$datos['contraseña']) && preg_match('/\W/',$datos['contraseña'])){
            $response = new Response();
            return $this->respuesta($response,['error'=> 'falta mayuscula,numero o caracter especial']); 
        }
        
        return $handler->handle($request);
    }
}
?>