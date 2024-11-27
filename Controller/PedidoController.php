<?php
    require_once '../Model/PedidoModel.php';

    class PedidoController {

        public function readAll($idCliente){
            $pedido = new Pedido();
            return $pedido->readAll($idCliente);
        }

        public function adicionarPedido($usuario){
            $pedido = new Pedido();
            return $pedido->adicionarPedido($usuario);
        }

        public function pedidoEntregue($usuario) {
            $pedido = new Pedido();
            return $pedido->pedidoEntregue($usuario);
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