<?php
    require_once '../Model/CupcakeModel.php';

    class CupcakeController {

        private $cupcake;

        public function __construct() {
            $this->cupcake = new Cupcake();    
        }

        public function readAll(){
            return $this->cupcake->readAll();
        }

        public function getCupcake($id){
            return $this->cupcake->getCupcake($id);
        }

        public function getDescricao($id){
            return $this->cupcake->getDescricao($id);
        }

        public function adicionarPersonalizado($massa, $recheio, $cobertura){

            try {
                $this->cupcake->adicionarPersonalizado($massa, $recheio, $cobertura);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        public function verificarRestricoes($restricoesCupcake, $restricoesUsuario) {
            return $this->cupcake->verificarRestricoes($restricoesCupcake, $restricoesUsuario);
        }

    }
?>