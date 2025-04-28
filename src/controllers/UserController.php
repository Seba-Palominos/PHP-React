<?php
//nombre de la ruta para llamarla
namespace App\controllers; 
 //cuando la llame lo hare con 'use App\controllers\UserController'}
 use DateTime;
 use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
 use App\Models\User;
 use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\services\Respuesta;
class UserController{ 

    public function login(Request $request, Response $response){
        //se trae el cuerpo de la solicitud http (datos de la peticion)
        $datos = $request->getParsedBody(); //getParsedBody trae los datos y los convierte en un array asociativo 
        //utilizo metodo logear de User
        $ok = User::logear($datos['usuario'],$datos['contraseña']);
        //compruebo si el usuario pudo iniciar sesion
        if ($ok){
           $playload = ['data'=>[
            'id'=> $ok['id']
            ]
           ];
           $key = 'la_calve_de_la_triple_s';
           $token = JWT::encode($playload,$key,'HS256');
            $zona_horaria = 'America/Argentina/Buenos_Aires';   //ajusto la zona horaria en la que va a vecer el token creado 
            $zona= new \DateTimeZone($zona_horaria);
            $fecha = new DateTime();
            $fecha->setTimezone($zona);
            $fecha->modify('+1 hour');
            $fechaSQL = $fecha->format('Y-m-d H:i:s');
            User::guardarToken($datos['usuario'],$token,$fechaSQL);
            return Respuesta::respuesta($response,["token" => $token],202);
        }else{
           return Respuesta::respuesta($response,["error"=> "error al iniciar sesion, no se esncuentra usuario"],400);
        }
    }
    public function registro(Request $request, Response $response){
        $datos = $request->getParsedBody();
        $aux = User::registrar($datos['nombre'],$datos['usuario'],$datos['contraseña']);
        if ($aux){
            return Respuesta::respuesta($response,["mensaje" =>"registro completado"],201);
        }else{
            return Respuesta::respuesta($response,["error" =>"el usuario posiblemente ya existe"],400);
        }

    }

    public function actualizar(Request $request, Response $response, array $args){
             $datos = $request->getParsedBody();
             $aux = User::actualizar($args['usuario'],$datos['nombre'],$datos['contraseña']);
             if ($aux){
                return Respuesta::respuesta($response,["mensaje" =>"datos actualizados"],200);
             }
             else{
                return Respuesta::respuesta($response,["mensaje" =>"no se pudo actualizar los datos"],400);
             }
   
    }

    public function traerDatos(Request $request, Response $response, array $args){
        $datos = User::traerDatos($args['usuario']);
        if ($datos != null){
          return Respuesta::respuesta($response,["nombre"=>$datos['nombre'],"usuario" => $datos['usuario']],200);
        }else{
            return Respuesta::respuesta($response,["error" =>"no hay datos"],404);
        }    
    
    }

    
    
}
?>