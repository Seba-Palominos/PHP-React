<?php
    namespace App\controllers;
    use App\controller\BD;
    use Exception;
    //cuando se quiera acceder'use App\controllers\User'
    class User{
      public static function logear($usuario,$contraseña){
        //me conecto a la base de datos
        $cnx = BD::conectar();
        //consulta sql para saber si existe el usuario que trae $usuario
        $sql = "SELECT usuario,password FROM  usuario WHERE usuario= :user";
        $aux=$cnx->prepare($sql); //prepare trae un objeto PDOStatement
        $aux->bindParam(':user',$usuario); // relaciono el parametro :user con la variable
        $aux->execute(); // ejecuto la consulta


        return $raw = $aux->fetch(PDO::FETCH_ASSOC); //fetch(PDO::FETCH_ASSOC) metodo que devuelve la fila obtenida en un arreglo asociativo
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
        $password = password_hash($password, PASSWORD_BCRYPT);
        $aux->bindParam(':nombre',$nombre);
        $aux->bindParam(':usuario',$usuario);
        $aux->bindParam(':password',$password);
        $aux->execute();
        return $aux;
      }catch(Exception $e){
        echo'Error en el controlador del registro'.$e->getMessage();
        return false;
      }
      }
    }
?>