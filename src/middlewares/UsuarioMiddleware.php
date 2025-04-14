<?php
namespace app\middlewares;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
 
 class UsuarioMiddleware implements MiddlewareInterface{
    public  function process( ServerRequestInterface $request,RequestHandlerInterface $handler): ResponseInterface{
        $datos = $request->getParsedBody();
        $ok = true;
        foreach($datos as $key => $value){
            if(empty($value) ){
                $ok = false;
            }
        }
        if ( $ok == false ){
            $response = new Response();
            $response->getBody()->write(json_encode(['error'=>'faltan datos en alguno de los campos']));
            $response = $response->withHeader('content-type','application/json')->withStatus(400);
            return $response;
        }
        return $handler->handle($request);
    }
    
}



?> 