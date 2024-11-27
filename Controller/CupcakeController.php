<?php
    require_once '../Model/CupcakeModel.php';

    class CupcakeController {

        public function readAll(){
            $cupcake = new Cupcake();
            return $cupcake->readAll();
        }

        public function getCupcake($id){
            $cupcake = new Cupcake();
            return $cupcake->getCupcake($id);
        }

        public function getDescricao($id){
            $cupcake = new Cupcake();
            return $cupcake->getDescricao($id);
        }

        public function adicionarPersonalizado($massa, $recheio, $cobertura){
            $cupcake = new Cupcake();

            try {
                $cupcake->adicionarPersonalizado($massa, $recheio, $cobertura);

            } catch (Exception $e) {
                // echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        public function verificarRestricoes($restricoesCupcake, $restricoesUsuario) {
            $cupcake = new Cupcake();
            return $cupcake->verificarRestricoes($restricoesCupcake, $restricoesUsuario);
        }

    }

    // $controller = new CupcakeController();
    // $cupcake = $controller->readAll();
    // foreach ($cupcake as $r) {
    //     echo "Id: " . $r['idCupcake'] . '<br>';
    //     echo "Sabor: " . $r['sabor'] . '<br>';
    //     echo "Descricao: " . $r['descricao'] . '<br>';
    //     echo "Preco: " . $r['preco'] . '<br>';
    // }
?>