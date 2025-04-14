<?php
//nombre de la ruta para llamarla
namespace app\controllers; 
 //cuando la llame lo hare con 'use App\controllers\UserController'}
 use DateTime;
 use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
 use App\Models\User;
class UserController{ 
    public function respuesta($mensaje,$estado,Response $response){
        $response->getBody()->write(json_encode($mensaje));
        $response = $response->withHeader("Content-Type","application/json")->withStatus($estado);
        return $response;
    }
    public function login(Request $request, Response $response){
        //se trae el cuerpo de la solicitud http (datos de la peticion)
        $datos = $request->getParsedBody(); //getParsedBody trae los datos y los convierte en un array asociativo 
        //utilizo metodo logear de User
        $ok = User::logear($datos['usuario'],$datos['contraseña']);
        //compruebo si el usuario pudo iniciar sesion
        if ($ok){
            $token = bin2hex(random_bytes(32)); //genero token con bin2hex, random_bytes indica que se genera un string random y dentro lleva como parametro la cantidad de bytes
            $zona_horaria = 'America/Argentina/Buenos_Aires';   //ajusto la zona horaria en la que va a vecer el token creado 
            $zona= new \DateTimeZone($zona_horaria);
            $fecha = new DateTime();
            $fecha->setTimezone($zona);
            $fecha->modify('+1 hour');
            $fechaSQL = $fecha->format('Y-m-d H:i:s');
            User::guardarToken($datos['usuario'],$token,$fechaSQL);
            return $this->respuesta(["token" => "$token"],202,$response);
        }else{
           return $this->respuesta(["error"=> "error al iniciar sesion, no se esncuentra usuario"],400,$response);
        }
    }
    public function registro(Request $request, Response $response){
        $datos = $request->getParsedBody();
        $aux = User::registrar($datos['nombre'],$datos['usuario'],$datos['contraseña']);
        if ($aux){
            return $this->respuesta(["mensaje" =>"registro completado"],201,$response);
        }else{
            return $this->respuesta(["error" =>"el usuario posiblemente ya existe"],400,$response);
        }

    }

    public function actualizar(Request $request, Response $response, array $args){
             $datos = $request->getParsedBody();
             $aux = User::actualizar($args['usuario'],$datos['nombre'],$datos['contraseña']);
             if ($aux){
                return $this->respuesta(["mensaje" =>"datos actualizados"],200,$response);
             }
             else{
                $response->getBody()->write(json_encode(["mensaje"=>"no se pudo actualizar los datos"]));
                return $this->respuesta(["mensaje" =>"no se pudo actualizar los datos"],400,$response);
             }
   
    }

    public function traerDatos(Request $request, Response $response, array $args){
        $datos = User::traerDatos($args['usuario']);
        if ($datos != null){
            return $this->respuesta(["nombre"=>$datos['nombre'],"usuario" => $datos['usuario']],200,$response);
        }else{
            return $this->respuesta(["error" =>"no hay datos"],404,$response);
        }    
    
    }

    
    
}
?>