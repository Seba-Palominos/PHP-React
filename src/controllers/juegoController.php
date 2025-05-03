<?php
namespace App\controllers;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\models\Juego;
use App\models\Mazo;
use App\models\MazoCarta;
use App\services\Respuesta;
class juegoController{

    public function pertenece(Request $request, Response $response){
        try{
        $datos = $request->getParsedBody();
        $zona_horaria = 'America/Argentina/Buenos_Aires';   //ajusto la zona horaria en la que va a vecer el token creado 
        $zona= new \DateTimeZone($zona_horaria);
        $fecha = new \DateTime();
        $fecha->setTimezone($zona);
        $token = $request->getHeaderLine('Authorization');
        $decodificado = JWT::decode($token,new Key('la_calve_de_la_triple_s','HS256'));
        $idUsuario = $decodificado->data->id;
        $dPartida=Juego::guardarPartida($idUsuario,$fecha->format('Y-m-d H:i:s'),$datos['idMazo'],'en_curso');
        if( empty($dPartida) ){
            return Respuesta::respuesta($response,['error'=>'no se pudo guardar partida'],404);            
        }
        if(!Mazo::actualizarEstado("en_mano",$datos['idMazo']))
            return Respuesta::respuesta($response,['error'=>'no se pudo actualizar estado de mazo'],404);
        $cartas = MazoCarta::getDatos($datos['idMazo']);
        if(empty($cartas)){
            return Respuesta::respuesta($response,["msj"=>"no hay datos de cartas"],400);
        }
        return Respuesta::respuesta($response,['id_partida'=> $dPartida,'carta'=>$cartas],200);
        }
        catch(Exception $e){
            return Respuesta::respuesta($response,["error en peticion"=>$e->getMessage()],500);
        }    
    }
 
}
?>