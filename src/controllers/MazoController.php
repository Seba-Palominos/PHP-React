<?php
namespace App\controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
 use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
 use App\models\Mazo;
 use App\services\Respuesta;

class MazoController{
    public function respuesta(){

    }
    public function recibirCartas(Request $request,Response $response){
        $datos = $request->getParsedBody();
        $token = $request->getHeaderLine('authorization');
        $decod = JWT::decode($token,new Key('la_calve_de_la_triple_s','HS256'));
        $idMazo = Mazo::crearMazo($decod->data->id,$datos['nombre']);
        if (!$idMazo){   
            return Respuesta::respuesta($response,['error'=>'no se pudo crear mazo'],400);
        }
        foreach($datos as $value){
            if (is_int($value)){
                $ok=Mazo::mazo_carta($value,$idMazo,'en_mazo');
                if (!$ok)
                return Respuesta::respuesta( $response,['error'=>'no se pudo guardar carta en el mazo'],400);
            }
        }
        return Respuesta::respuesta( $response,['nombre mazo'=> $datos['nombre'],'id mazo'=>$idMazo],200);
    }
    public function cartas(Request $request, Response $response, array $args) {
        $queryParams = $request->getQueryParams();
        $atributo = $queryParams['atributo'] ?? null;
        $nombre = $queryParams['nombre'] ?? null;
    
        $cartas =Mazo::buscarCartas($atributo, $nombre);
    
        if (!empty($cartas)) {
            return Respuesta::respuesta($response,$cartas, 200, );
        } else {
            return Respuesta::respuesta($response,["error" => "No se encontraron cartas"], 404 );
        }
    }

    public function actualizarMazo(Request $request, Response $response, array $args): Response {
        $userId = $request->getAttribute('userId');
        $mazoId = (int)$args['mazo'];
        $data = $request->getParsedBody();
        $nuevoNombre = $data['nombre'] ?? null;
        
        if (!$nuevoNombre) {
            $response->getBody()->write(json_encode(["error" => "El campo 'nombre' es requerido."]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }       

        $ok = Mazo::cambiarNombreMazo($mazoId, $nuevoNombre, $userId);
    
        if (!$ok) {
            $response->getBody()->write(json_encode(["error" => "Mazo no encontrado o no pertenece al usuario."]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    

        $response->getBody()->write(json_encode(["mensaje" => "Mazo actualizado correctamente."]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    public function obtenerMazo(Request $request, Response $response, array $args): Response {
        $tokenUserId = $request->getAttribute('userId');
        $paramUserId = (int)$args['usuario'];
    
        if (!$tokenUserId) {
            $response->getBody()->write(json_encode(["error" => "No autorizado"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    
        if ($tokenUserId !== $paramUserId) {
            $response->getBody()->write(json_encode(["error" => "No tienes permiso para ver estos mazos."]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }
    
        $mazos = Mazo::obtenerMazoId($tokenUserId);
    
        $response->getBody()->write(json_encode(["mazos" => $mazos]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function deleteMazo(Request $request, Response $response, array $args): Response {
        $userId = $request->getAttribute('userId');
        $mazoId = (int)$args['mazo'];
    
        try {
            $deleted = Mazo::borrarMazo($mazoId, $userId);
    
            if (! $deleted) {
                $response->getBody()->write(json_encode(["error" => "Mazo no encontrado o no pertenece al usuario."]));
                return $response
                    ->withStatus(404)
                    ->withHeader("Content-Type", "application/json");
            }
    
            $response->getBody()->write(json_encode(["msj" => "Mazo eliminado correctamente."]));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
    
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([ "error" => "Error al eliminar el mazo: " . $e->getMessage()  ]));
            return $response->withStatus(409)->withHeader("Content-Type", "application/json");

                
        }
    }


}

?>