<?php
    namespace App\models;
    use App\models\BD;
    class Mazo{
        public static function cantMazos($id){
            $sql = "SELECT COUNT(*) AS cantidad FROM mazo WHERE usuario_id = :id";
            $conn = BD::conectar();
            $cons = $conn -> prepare($sql);
            $cons ->bindParam(":id", $id);
            $cons -> execute();
            $datos = $cons -> fetch(\PDO::FETCH_ASSOC);
            return ($datos['cantidad'] < 3) ? true : false ;
        }
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
            if ($consulta->execute())
                return $conn->lastInsertId();
            return false;
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
        public static function buscarCartas($atributo , $nombre ) {
            // agregar try y catch
                // me conecto a la base de datos
                $cnx = BD::conectar();
                
                // selecciono toda las cartas
                $sql = "SELECT id, nombre, atributo, puntos_ataque FROM carta";
                
                $conditions = [];
                $params = [];
            
                if ($atributo !== null) {
                    $conditions[] = "atributo = :atributo";
                    $params[':atributo'] = $atributo;
                }
            
                if ($nombre !== null) {
                    $conditions[] = "nombre LIKE :nombre";
                    $params[':nombre'] = "%$nombre%";
                }
            
                // si se cumple se lo agrego a sql
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(" AND ", $conditions);
                }
            
                $stmt = $cnx->prepare($sql);
                $stmt->execute($params);
            
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public static function actualizarEstado($estado,$idMazo){
            $sql = "UPDATE mazo_carta SET estado = :estado WHERE mazo_id = :id";
            $cnx = BD::conectar();
            $consulta = $cnx->prepare($sql);
            $consulta->bindParam(":estado", $estado);
            $consulta->bindParam(":id", $idMazo);
            $consulta->execute();
            return $consulta;
        }
        public static function cambiarNombreMazo($mazoId, $nuevoNombre, $userId) {

            $cnx = BD::conectar();
            $sql = "UPDATE MAZOS SET NOMBRE = :nombre WHERE id = mazoId and usuarioId =: userId";
            $stmt = $cnx->prepare($sql);

            $stmt->bindParam(':nombre', $nuevoNombre);
            $stmt->bindParam(':mazoId', $mazoId, \PDO::PARAM_INT);
            $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;

        }   

        public static function obtenerMazoID($userId) {
            $cnx = BD::conectar();
            $sql = "SELECT * FROM mazos WHERE usuario_id = :id";
            $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        public static function borrarMazo(int $mazoId, int $userId): bool {
            // Verificar participación en partidas
            $cnx = BD::conectar();
            
            $sql = "SELECT 1 FROM partidas p JOIN mazos m ON p.mazo_id = m.id WHERE m.id = :mazoId AND m.usuario_id = :userId LIMIT 1";
            $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':mazoId', $mazoId, \PDO::PARAM_INT);
            $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->fetch()) {
                throw new \Exception("El mazo ya participó en una partida y no puede borrarse.");
            }
    
            $sql = ("DELETE FROM mazos WHERE id = :mazoId AND usuario_id = :userId");
            $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':mazoId', $mazoId, \PDO::PARAM_INT);
            $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }
      
    }
    

?>