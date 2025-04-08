<?php
//nombre de la ruta para llamarla
namespace app\controllers; 
 //cuando la llame lo hare con 'use App\controllers\UserController'}
 use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
 use App\Models\User;
class UserController{
    public function login(Request $request, Response $response){
        //se trae el cuerpo de la solicitud http (datos de la peticion)
        $datos = $request->getParsedBody(); //getParsedBody trae los datos y los convierte en un array asociativo 
        $datos['usuario'] ?? null;
        $datos['contraseña'] ?? null;
        //utilizo metodo logear de User
        $ok = User::logear($datos['usuario'],$datos['contraseña']);
        $pass = $ok['password'];

        //compruebo si el usuario pudo iniciar sesion
        if (password_verify($datos['contraseña'],$ok['password'])){
            $response->getBody()->write(json_encode(['msj:'=>'ingreso con exito']));
            $response = $response->withHeader('Content-type', 'application/json')->withStatus(202);
            return $response;
        }else{
            $response->getBody()->write(json_encode(["error"=>"error al iniciar sesion"]));
            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            return $response;
        }
    }
    public function registro(Request $request, Response $response){
        $datos = $request->getParsedBody();
        $aux = User::registrar($datos['nombre'],$datos['usuario'],$datos['password']);
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
        
    }
}
?>