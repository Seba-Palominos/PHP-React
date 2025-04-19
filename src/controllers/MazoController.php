<?php
namespace app\controllers;
use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
class MazoController{
    public function recibirCartas(Request $request,Response $response){
        try{
        $datos = $request->getParsedBody();
        $response->getBody()->write(json_encode(["msj" =>"ingrese"]));
        $response = $response->withHeader("Content-Type","application/json");
        return $response;
        }catch(\Exception $e){
            echo "error en ejecucion" . $e->getMessage();
            $response->getBody()->write(json_encode(["msj" =>"fallo procesamiento"]));
            $response = $response->withHeader("Content-Type","application/json");
            return $response;
        }
    }
}

?>