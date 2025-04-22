<?php
namespace app\controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
 use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
 use app\models\Mazo;
 use app\services\Respuesta;

class MazoController{
    public function respuesta(){

    }
    public function recibirCartas(Request $request,Response $response){
        $datos = $request->getParsedBody();
        $token = $request->getHeaderLine('authorization');
        $decod = JWT::decode($token,new Key('la_calve_de_la_triple_s','HS256'));
        $datosMazo = Mazo::crearMazo($decod->data->id,$datos['nombre']);
        if (!$datosMazo){   
            return Respuesta::respuesta($response,['error'=>'no se pudo crear mazo'],0);
        }
        foreach($datos as $value){
            if (is_int($value)){
                $ok=Mazo::mazo_carta($value,$datosMazo['id'],'en_mazo');
                if (!$ok)
                return Respuesta::respuesta( $response,['error'=>'no se pudo guardar carta en el mazo'],0);
            }
        }
        return Respuesta::respuesta( $response,['nombre mazo'=> $datos['nombre'],'id mazo'=>$datosMazo['id']],0);
    }
}

?>