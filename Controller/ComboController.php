<?php
    require_once '../Model/ComboModel.php';

    class ComboController {

        public function readAll(){
            $combo = new Combo();
            return $combo->readAll();
        }

        public function readCombo($idCombo){
            $combo = new Combo();
            return $combo->readCombo($idCombo);
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