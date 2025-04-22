<?php
    namespace App\models;
    use App\models\BD;
    class Mazo{
        public static function cartaValida($id){
            $cnx = BD::conectar();
            $sql = "SELECT id FROM carta WHERE id = :id";
            $aux =$cnx->prepare($sql);
            $aux ->bindParam(':id', $id);
            $aux -> execute();
            return ($aux->rowCount()>0)? true : false ;
        }
        public static function crearMazo($id,$nombre){
            $sql = 'INSERT INTO mazo (usuario_id,nombre) VALUES (:id,:nombre)';
            $conn = BD::conectar();
            $consulta = $conn -> prepare($sql);
            $consulta ->bindParam(':id', $id);
            $consulta->bindParam(':nombre',$nombre);
            $consulta->execute();
            return $consulta->fetch(\PDO::FETCH_ASSOC);
        }
        public static function mazo_carta($idCarta,$idMazo,$estado){
            $sql = 'INSERT INTO mazo_carta (carta_id,mazo_id,estado) VALUES (:idC,:idMazo,:estado)';
            $conn = BD::conectar();
            $consulta = $conn -> prepare($sql);
            $consulta->bindParam('idC', $idCarta);
            $consulta->bindParam('idMazo', $idMazo);
            $consulta->bindParam('estado', $estado);
            return $consulta->execute();

        }
    }

?>