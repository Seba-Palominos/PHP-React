<?php
namespace App\controllers;
use App\services\Jugadas;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\models\Juego;
use App\models\Mazo;
use App\models\MazoCarta;
use App\services\Respuesta;
use App\services\JugadaServidor;
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
        if(!Mazo::actualizarEstadoMazo("en_mano",$datos['idMazo']))
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
    public function crearJugada(Request $request, Response $response):Response{
        try {
            //Recibe la carta jugada por el usuario y el id de la partida
            $datos = $request->getParsedBody();
            $idPartida = $datos['idPartida'] ?? null;
            $idCarta = $datos['idCarta'] ?? null;

            //Verifica que la carta enviada sea valida para jugar
            if(!Juego::cartaValida($idPartida,$idCarta)){
                return Respuesta::respuesta($response,['error'=>'la carta elegida no es valida'],400);
            }
            //Crea un registro en la tabla "jugada".
            Mazo::actualizarEstadoMazo('en_mano',1);
            $idServ = JugadaServidor::jugadaServidor();
            $idJugada = Juego::crearJugada($idCarta,$idPartida,$idServ);
            if (!$idJugada){
                return Respuesta::respuesta($response,['error'=>'no se creo la jugada'],400);
            }
            //analiza cual es la carta ganadora
            
            //actualiza el estado de la carta en la tabla "mazo_carta" a estado "descartado"
            $estado = Juego::juego($idCarta,$idServ);
            //guarda en el registro "jugada" recientemente creado el estado final de la misma "gano","perdio" o "empato"
            if (!(Juego::actualizarEstadoJugada($estado,$idJugada))) {
                return Respuesta::respuesta($response,['error'=>'no se actualizo el estado de la jugada'],404);
            }
            MazoCarta::actualizarEstadoCarta('descartado',$idCarta);
            MazoCarta::actualizarEstadoCarta('descartado',$idServ);

            //Si, es la quinta jugada debe cerrar la partida con el estado correspondiente ("finalizada")
            if(Juego::esQuintaJugada($idPartida)){
                //lo comento porque no me lo pide el ejercicio pero hay q hacerlo xd
                if(!Juego::ganadorPartida($idPartida)){
                    return Respuesta::respuesta($response,['error'=>'no se actualizo el estado de la partida'],400);
                }

                if(!Juego::actualizarEstadoPartida('finalizada',$idPartida)){
                    return Respuesta::respuesta($response,['error'=>'no se actualizo el estado de la partida'],400);
                }
                $token = $request->getHeaderLine('Authorization');
                $decodificado = JWT::decode($token,new Key('la_calve_de_la_triple_s','HS256'));
                $idUsuario = $decodificado->data->id;
                $idMazoUsuario = MazoCarta::idMazo($idCarta);
                Mazo::actualizarEstadoMazo('en_mazo',$idMazoUsuario);   
                Mazo::actualizarEstadoMazo('en_mazo',1);               
            }
            return Respuesta::respuesta($response,[],200);
        }
        catch(Exception $e){
            return Respuesta::respuesta($response,["faltan datos"=>$e->getMessage()],500);
        }
    }

    
    public function cartasEnMano(Request $request, Response $response, array $args): Response{

        $usuarioIdParam = (int) $args['usuario'] ?? 0;
        $partidaId = (int) $args['partida'] ?? 0;
    
        if ($usuarioIdParam <= 0 || $partidaId <= 0) {
            $response->getBody()->write(json_encode(['error' => 'Parámetros inválidos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Bad Request
        }
    
    
        $cartas = Juego::obtenerCartasEnMano($usuarioIdParam, $partidaId);
    
        $response->getBody()->write(json_encode($cartas));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200); // OK
        }
 
}
?>