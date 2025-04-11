<?php
    namespace App\models;
    use App\models\BD;
    use DateTime;
    use Exception;
    //cuando se quiera acceder'use App\models\User'
    class User{
      public static function generarToken($usuario){
        //genero token con bin2hex, random_bytes indica que se genera un string random y dentro lleva como parametro la cantidad de bytes
        $token = bin2hex(random_bytes(32));

        //ajusto la zona horaria en la que va a vecer el token creado 
        $zona_horaria = 'America/Argentina/Buenos_Aires';
        $zona= new \DateTimeZone($zona_horaria);
        $fecha = new DateTime();
        $fecha->setTimezone($zona);
        $fecha->modify('+1 hour');
        $fechaSQL = $fecha->format('Y-m-d H:i:s');

        //Realizo la consulta sql
        $cnx = BD::conectar();
        $sql = 'UPDATE usuario SET token = :token, vencimineto_token = :fechaSQL where usuario = :usuario ';
        $result = $cnx->prepare($sql);
        $result->bindParam(':token',$token);
        $result->bindParam(':fechaSQL',$fechaSQL);
        $result->bindParam(':usuario',$usuario);

        //retorno el token si se realizo la consulta con exito
        $ok=$result->execute();
        if($ok){
          return $token;
        }
        return false;
      }
      public static function logear($usuario,$contraseña){
        //me conecto a la base de datos
        $cnx = BD::conectar();
        //consulta sql para saber si existe el usuario que trae $usuario
        $sql = "SELECT usuario,password FROM  usuario WHERE usuario= :user";
        $aux=$cnx->prepare($sql); //prepare trae un objeto PDOStatement
        $aux->bindParam(':user',$usuario); // relaciono el parametro :user con la variable
        $aux->execute(); // ejecuto la consulta


        return $raw = $aux->fetch(\PDO::FETCH_ASSOC); //fetch(PDO::FETCH_ASSOC) metodo que devuelve la fila obtenida en un arreglo asociativo
      }
      public static function registrar($nombre,$usuario,$password){
        try{
          //compruebo si el usuario ya existe
        $cnx = BD::conectar();
        $chequeoSql = 'SELECT COUNT(*) FROM usuario WHERE usuario = :user' ;
        $stmt = $cnx->prepare($chequeoSql);
        $stmt ->bindParam(':user',$usuario);
        $stmt -> execute();
        if ($stmt ->fetchColumn() > 0){
          return false;
        }
        //insertar nuevos datos a la tabla        
          $sql = 'INSERT INTO usuario(nombre,usuario,password) VALUES(:nombre,:usuario,:password)';
          $aux=$cnx->prepare($sql);
          $aux->bindParam(':nombre',$nombre);
          $aux->bindParam(':usuario',$usuario);
          $aux->bindParam(':password',$password);
          $aux->execute();
          return true;
      }catch(Exception $e){
        echo'Error en el controlador del registro'.$e->getMessage();
        return false;
      }
      }
      public static function tokenVencido($usuario){
        $sql = 'SELECT vencimiento_token FROM usuario WHERE usuario = :user ';
        $cnx = BD::conectar();
        $peticion = $cnx->prepare($sql);
        $peticion->bindParam(':user',$usuario);
        $peticion->execute();
        return $peticion->fetch(\PDO::FETCH_ASSOC);
      }
      public static function actualizarDatos($usuario,$contraseña){

        return true;
      }
      public static function datosUsuario($usuario){
        $sql = 'SELECT nombre,';
      }
    }
?>