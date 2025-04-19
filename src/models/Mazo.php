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
    }

?>