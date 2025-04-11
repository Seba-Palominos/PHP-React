<?php
//nombre de la ruta para llamarla
namespace app\controllers; 
 //cuando la llame lo hare con 'use App\controllers\UserController'}
 use DateTime;
 use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
 use App\Models\User;
class UserController{ 
    public function login(Request $request, Response $response){
        //se trae el cuerpo de la solicitud http (datos de la peticion)
        $datos = $request->getParsedBody(); //getParsedBody trae los datos y los convierte en un array asociativo 
        //utilizo metodo logear de User
        $ok = User::logear($datos['usuario'],$datos['contraseña']);
        

        //compruebo si el usuario pudo iniciar sesion
        if ($ok){
            $token = User::generarToken($datos['usuario']);
            $response->getBody()->write(json_encode(['token:'=>$token]));
            $response = $response->withHeader('Content-type', 'application/json')->withStatus(202);
            return $response;
        }else{
            $response->getBody()->write(json_encode(["error"=>"error al iniciar sesion, usuario o contraseña incorrecta"]));
            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            return $response;
        }
    }
    public function registro(Request $request, Response $response){
        $datos = $request->getParsedBody();
        $aux = User::registrar($datos['nombre'],$datos['usuario'],$datos['contraseña']);
        if ($aux){
             $response->getBody()->write(json_encode(['mensaje:'=>'registro completado']));
            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            return $response;
        }else{
            $response->getBody()->write(json_encode(['error:'=> 'no se pudo registrar el usuario,el usuario posiblemente ya existe']));
            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            return $response;
        }

    }

    public function actualizar(Request $request, Response $response, array $args){
        
        $data = User::tokenVencido($args['usuario']);
        $zonaHoraria = new DateTimeZone('America/Argentina/Buenos_Aires');
        $actual = new DateTime();
        $actual ->setTimezone($zonaHoraria);
        $hora = $actual->format('Y-m-d H:i:s');
        if ($hora >= $data){
            $response ->getBody()->write(json_encode(['mensaje'=> 'expiro el inicio de sesion, vuelva a ingresar']));
            return $response;
        }
        $datos = $request->getParsedBody();
        $ok=User::actualizarDatos($datos['usuario'],$datos['contraseña']);
        if ($ok){
            $response->getBody()->write(json_encode(['mensaeje'=> 'se actualizaron los datos']));
            $response = $response->withHeader('content-type', 'application/json')->withStatus(201);   
        }
        else{
            $response->getBody()->write(json_encode(['mensaeje'=> 'no se pudo actualizar los datos']));
            $response = $response->withHeader('content-type', 'application/json')->withStatus(400);
        }
        return $response;
    }

    public function traerDatos(Request $request, Response $response, array $args){
        User::datosUsuario($args['usuario']);
    
    }
}
?>