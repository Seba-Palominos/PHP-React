<?php
namespace app\middlewares;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use App\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class ValidacionToken implements MiddlewareInterface{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $token = $request->getHeaderline("Authorization");
        if(empty($token)){
            $response = new Response();
            $response->getBody()->write(json_encode(["error"=>"no se envio token"]));
            $response = $response->withHeader("Content-Type","application/json");
            return $response;
        }else{
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }
            $decodificado = JWT::decode($token,new Key('la_calve_de_la_triple_s','HS256'));
            $id = $decodificado->data->id;
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