<?php
    namespace App\models;
    use App\models\BD;
    class MazoCarta{
        public static function getDatos(int $idMazo){
                $sql = "SELECT nombre,ataque,ataque_nombre,imagen,atributo
                        FROM mazo_carta mc
                        JOIN carta c ON mc.carta_id = c.id
                        WHERE mc.mazo_id = :idMazo
                          AND mc.estado = 'en_mazo'";  // Podrías quitar esta línea si querés todos
            
                $cnx = BD::conectar();
                $consulta = $cnx->prepare($sql);
                $consulta->bindParam(":idMazo", $idMazo, \PDO::PARAM_INT);
                $consulta->execute();
                
                return $consulta->fetchAll(\PDO::FETCH_ASSOC);
            }
        }
    
?>