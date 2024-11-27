<?php
    require_once '../Model/CarrinhoModel.php';

    class CarrinhoController {

        public function exibirCarrinho($userId){
            $carrinho = new Carrinho();
            return $carrinho->exibirCarrinho($userId);
        }

        public function adicionarAoCarrinho($userId, $id, $tipo, $operacao){
            $carrinho = new Carrinho();
            
            try {
                $carrinho->adicionarAoCarrinho($userId, $id, $tipo, $operacao);
                echo json_encode(['success' => true, 'message' => $operacao]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        public function repetirPedido($pedido){
            $carrinho = new Carrinho();
            $carrinho->repetirPedido($pedido);
        }

        public function criarCarrinho($email) {
            $carrinho = new Carrinho();
            $carrinho->criarCarrinho($email);
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