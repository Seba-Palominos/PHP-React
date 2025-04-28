<?php 
namespace App\models;
use App\models\BD;
class Juego{
    public static function guardarPartida(int $id_usuario,string $fecha,int $id_mazo,string $estado){
        $sql = "INSERT INTO partida (usuario_id,fecha,mazo_id,estado) VALUES (:id_usuario,:fecha,:id_mazo,:estado)";
        $cnx = BD::conectar();
        $consulta = $cnx->prepare($sql);
        $consulta->bindParam(":id_usuario", $id_usuario);
        $consulta->bindParam(":fecha", $fecha);
        $consulta->bindParam(":id_mazo", $id_mazo);
        $consulta->bindParam(":estado", $estado);
        if ($consulta->execute()){
            return $cnx->lastInsertId();
        }else return false;
    }
}
?>