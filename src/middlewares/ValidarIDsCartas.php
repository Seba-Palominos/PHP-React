<?php
namespace app\middlewares;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use App\models\Mazo;
class ValidarIDsCartas implements MiddlewareInterface{
    public function elementosIguales(array $datos){
        $cant = count($datos);
        $cantAct = array_unique($datos);
        return ($cant != count($cantAct)? true : false);
    }
    public function respuesta(Response $response,array $msj):Response{
        $response->getBody()->write(json_encode($msj));
        $response = $response->withHeader("Content-Type","application/json");
        return $response;
    }
public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
    $ids = $request->getParsedBody();
    $copia =[];
    foreach($ids as $values){
        if (is_int($values))
            $copia[]=$values;
    }
    if (count($copia) != 5) 
        return $this->respuesta(new Response(),["error"=> "no selecciono 5 cartas"]);

    if ($this->elementosIguales($ids))
        return $this->respuesta(new Response(),["error"=> "hay cartas iguales en el mazo"]);
    $cumple = true;
    foreach ($copia as $values){
        if (!Mazo::cartaValida($values)){
            $cumple = false;
            break;
        }
    }
    if ($cumple === false)
        return $this-> respuesta(new Response(),["error"=> "hay algun id inexistente"]);

    return $handler->handle($request);
}

}


?>