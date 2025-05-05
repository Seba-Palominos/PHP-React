<?php
namespace App\models;
    use App\models\BD;
    use Exception;
    class UserEstadistica{
        public $ganadas = 0;
        public $perdidas = 0;
        public $empatadas = 0;

        public function sumar($estado){
            switch($estado){
                case 'gano':
                    $this->ganadas++;
                    break;
                case 'empato':
                    $this->empatadas++;
                    break;
                case 'perdio':
                    $this->perdidas++;
                    break;
            }
        }
        public function aArray(){
            return [
                'ganadas' => $this->ganadas,
                'empatadas' => $this->empatadas,
                'perdidas' => $this->perdidas
            ];
        }
    }
    class Estadisticas{
        public static function estadisticas() {
            $cnx = BD::conectar();
            $sql = "SELECT usuario_id, el_usuario, COUNT(*) as cantidad FROM partida GROUP BY usuario_id, el_usuario";
            $stmt = $cnx->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
            $estadisticas = [];
        
            foreach ($resultados as $fila) {
                $id = $fila['usuario_id'];
                $estado = $fila['el_usuario'];
                $cantidad = $fila['cantidad'];
        
                if (!isset($estadisticas[$id])) {
                    $estadisticas[$id] = new UserEstadistica();
                }
        
                for ($i = 0; $i < $cantidad; $i++) {
                    $estadisticas[$id]->sumar($estado);
                }
            }
        
            // Convertimos a array para devolver como JSON
            $response = [];
            foreach ($estadisticas as $id => $objeto) {
                $response[$id] = $objeto->aArray();
            }
        
            return $response;
        }        
    }
?>