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

    public static function cartaValida(int $idPartida,int $idCarta){
        $sql = "SELECT mazo_carta.estado FROM partida INNER JOIN mazo_carta ON partida.mazo_id = mazo_carta.mazo_id WHERE partida.id = :idPartida AND mazo_carta.carta_id = :idCarta";
        $cnx = BD::conectar();
        $consulta = $cnx->prepare($sql);
        $consulta->bindParam(':idPartida',$idPartida);
        $consulta->bindParam(':idCarta',$idCarta);
        $consulta->execute();
        $aux = $consulta->fetch(\PDO::FETCH_ASSOC);
        if(!empty($aux) && $aux['estado'] != "descartado"){
            return  true;
        }
        return false;//400 error no coincide la carta con el mazo
    }
    public static function crearJugada(int $idCarta,int $idPartida,int $idCartaServ){
        $cnx = BD::conectar();
        $sql = "INSERT INTO jugada(carta_id_a,carta_id_b,partida_id) VALUES (:idCarta,:idCartaServ,:idPartida)";
        $aux = $cnx->prepare($sql);
        $aux->bindParam(':idCarta',$idCarta);
        $aux->bindParam(':idCartaServ',$idCartaServ);
        $aux->bindParam(':idPartida',$idPartida);
        if($aux->execute()){
            return  $cnx->lastInsertId();//200
        }
        return false;
    }
    public static function juego(int $idCartaUsuario,int $idCartaServidor){
        
            $cnx = BD::conectar();
        
            // 1. Obtener atributos y ataques de ambas cartas
            $sql = "SELECT id, ataque, atributo_id FROM carta WHERE id IN (:id1, :id2)";
            $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':id1', $idCartaUsuario);
            $stmt->bindParam(':id2', $idCartaServidor);
            $stmt->execute();
            $cartas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
            foreach ($cartas as $carta) {
                if ($carta['id'] == $idCartaUsuario) {
                    $ataqueUsuario = (float)$carta['ataque'];
                    $atributoUsuario = (int)$carta['atributo_id'];
                } else {
                    $ataqueServidor = (float)$carta['ataque'];
                    $atributoServidor = (int)$carta['atributo_id'];
                }
            }
        
            if (!isset($ataqueUsuario) || !isset($ataqueServidor)) {
                throw new \Exception("Faltan datos de cartas.");
            }
        
            // 2. Verificar si el atributo del usuario le gana al del servidor
            $sql = "SELECT 1 FROM gana_a WHERE atributo_id = :usuario AND atributo_id2 = :servidor";
            $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':usuario', $atributoUsuario);
            $stmt->bindParam(':servidor', $atributoServidor);
            $stmt->execute();
        
            if ($stmt->fetch()) {
                $ataqueUsuario *= 1.3; // 30% de bonus
            }
        
            // 3. Comparar y devolver resultado
            if ($ataqueUsuario > $ataqueServidor) {
                return "ganó";
            } elseif ($ataqueUsuario < $ataqueServidor) {
                return "perdió";
            } else {
                return "empató";
            }
        }
        public static function actualizarEstadoJugada($estado, $id_jugada){
            $cnx = BD::conectar();
            $sql = "UPDATE jugada SET el_usuario = :estado WHERE id = :id_jugada";
            $aux =$cnx->prepare($sql);
            $aux->bindParam(':id_jugada',$id_jugada);
            $aux->bindParam(':estado',$estado);
            $aux->execute();
            return $aux->rowCount()>0;
        }
    
        public static function esQuintaJugada($idPartida){
            $cnx = BD::conectar();
            $sql = "SELECT COUNT(*) as cantidad FROM jugada WHERE partida_id = :idPartida";
            $aux = $cnx->prepare($sql);
            $aux->bindParam(':idPartida',$idPartida);
            $aux->execute();
            $cant = $aux->fetch(\PDO::FETCH_ASSOC);
            return (isset($cant['cantidad']) && $cant['cantidad'] == 5) ? true:false ;
        }
        public static function ganadorPartida($idPartida){
            $cnx = BD::conectar();
            $sql = "SELECT estado, COUNT(*) as cantidad FROM jugada WHERE partida_id = :idPartida GROUP BY estado";
            $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':idPartida',$idPartida);
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $estadisticas = [
                'gano' => 0,
                'perdio' => 0,
                'empato' => 0
            ];
            foreach ($resultados as $fila) {
                $estadisticas[$fila['estado']] = $fila['cantidad'];
            }
            if($estadisticas['gano'] >$estadisticas['perdio']){
                $estado = 'gano';
            }elseif($estadisticas['perdio'] >$estadisticas['gano']){
                $estado = 'perdio';
            } else {
                $estado = 'empato';
            }
            return Juego::actualizarEstadoPartidaUsuario($estado,$idPartida);
        }
        public static function actualizarEstadoPartidaUsuario($estado, $idPartida){
            $cnx = BD::conectar();
            $sql = "UPDATE partida SET el_usuario = :estado WHERE id = :idPartida";
            $aux =$cnx->prepare($sql);
            $aux->bindParam(':idPartida',$idPartida);
            $aux->bindParam(':estado',$estado);
            $aux->execute();
            return $aux->rowCount()>0;
        }
        public static function actualizarEstadoPartida($estado, $idPartida){
            $cnx = BD::conectar();
            $sql = "UPDATE partida SET estado = :estado WHERE id = :id_jugada";
            $aux =$cnx->prepare($sql);
            $aux->bindParam(':id_jugada',$idPartida);
            $aux->bindParam(':estado',$estado);
            $aux->execute();
            return $aux->rowCount()>0;
        }
    
    public  static function obtenerCartasEnMano(int $usuarioId, int $partidaId): array  {
        try {
            $cnx = BD::conectar();
    
            $sql = "
            SELECT atributo.nombre AS atributo
            FROM mazo_carta
            JOIN carta ON mazo_carta.carta_id = carta.id
            JOIN atributo ON carta.atributo_id = atributo.id
            JOIN mazo ON mazo_carta.mazo_id = mazo.id
            JOIN partida ON partida.mazo_id = mazo.id
            WHERE partida.id = :partidaId
            AND mazo.usuario_id = :usuarioId
            AND mazo_carta.estado = 'en_mano'";
            
            $stmt = $cnx->prepare($sql);
    
            $stmt-> bindParam(':usuarioId', $usuarioId);
            $stmt-> bindParam(':partidaId', $partidaId);
    
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
        } catch (\PDOException $e) {
            return ["error" => "error: " . $e->getMessage()];
            }
        }
    

}
?>