<?php
    namespace App\models;
    use App\models\BD;
    class MazoCarta{
        public static function getDatos( $idMazo){
            $sql ="SELECT carta.nombre FROM carta INNER JOIN mazo_carta ON carta.id = mazo_carta.carta_id WHERE mazo_carta.mazo_id = :idMazo";
            $cnx = BD::conectar();
            $consulta = $cnx->prepare($sql);
            $consulta->bindParam(":idMazo", $idMazo);
            $consulta->execute();    
            return $consulta->fetchAll(\PDO::FETCH_ASSOC);
            }
        }
    
?>