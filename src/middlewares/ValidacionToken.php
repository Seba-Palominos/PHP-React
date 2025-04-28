<?php
namespace App\middlewares;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use App\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\services\Respuesta;
class ValidacionToken implements MiddlewareInterface{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $token = $request->getHeaderline("Authorization");
        if(empty($token)){
            return Respuesta::respuesta(new Response(),["error"=>"no se envio token"],400);
        }else{
            try{
            $decodificado = JWT::decode($token,new Key('la_calve_de_la_triple_s','HS256'));
            $id = $decodificado->data->id;
            if (!User::validarToken($token,$id) ){
                return Respuesta::respuesta(new Response(),["error"=> "Inicio de sesion expirado o no se encuentra token"],400);
            }
            return $handler->handle($request);
        }catch(\Throwable $e){
            return Respuesta::respuesta(new Response(),["error"=> "Inicio de sesion expirado o no se encuentra token","detalle"=>$e->getMessage()],400);
        }
    }}
}

?>