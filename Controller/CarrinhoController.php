<?php
    require_once '../Model/CarrinhoModel.php';

    class CarrinhoController {

        private $carrinho;

        public function __construct() {
            $this->carrinho = new Carrinho();
        }

        public function exibirCarrinho($userId){
            return $this->carrinho->exibirCarrinho($userId);
        }

        public function adicionarAoCarrinho($userId, $id, $tipo, $operacao){
            
            try {
                $this->carrinho->adicionarAoCarrinho($userId, $id, $tipo, $operacao);
                echo json_encode(['success' => true, 'message' => $operacao]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        public function repetirPedido($pedido){
            $this->carrinho->repetirPedido($pedido);
        }

        public function criarCarrinho($email) {
            $this->carrinho->criarCarrinho($email);
        }

        public function getCart($userId){
            $this->carrinho->getCart($userId);
        }

    }
?>