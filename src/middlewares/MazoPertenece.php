<?php
    namespace App\middlewares;
    use App\services\Respuesta;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Slim\Psr7\Response;
    use App\services\Pertenece;
    class MazoPertenece implements MiddlewareInterface{
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
            //guardo los datos del body
            $idMazo = $request->getParsedBody();
            $token = $request->getHeaderLine("Authorization");
            //decodifico token para recuperar id dentro de try catch
            try{
            $decod = JWT::decode($token,new Key('la_calve_de_la_triple_s','HS256'));
            $idUsuario = $decod->data->id;}
            catch(\Exception $e){
                return Respuesta::respuesta(new Response(), ['error' => 'Token inválido'], 401);
            }
            //respuesta si no pertenece el mazo al usuario
            if (!Pertenece::pertenece($idMazo['idMazo'],$idUsuario)){
               return Respuesta::respuesta(new Response(),['error'=>'mazo no pertenece al usuario'],400); 
            }
            return $handler->handle($request);
        }
    }

?>