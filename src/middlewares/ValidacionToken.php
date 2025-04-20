<?php
namespace app\middlewares;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use App\models\User;

class ValidacionToken implements MiddlewareInterface{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $token = $request->getHeaderline("Authorization");
        if(empty($token)){
            $response = new Response();
            $response->getBody()->write(json_encode(["error"=>"no se envio token"]));
            $response = $response->withHeader("Content-Type","application/json");
            return $response;
        }else{
            $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
            $route = $routeContext->getRoute();
            $id = $route->getArgument("usuario");
            if (!User::validarToken($token,$id) ){
                $response = new Response();
                $response->getBody()->write(json_encode(["error"=> "Inicio de sesion expirado o no se encuentra token"]));
                $response = $response->withHeader("Content-Type","application/json");
                return $response;
            }
        }
        return $handler->handle($request);
    }
}

?>