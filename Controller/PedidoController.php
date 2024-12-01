<?php
    require_once '../Model/PedidoModel.php';

    class PedidoController {

        private $pedido;
        
        public function __construct() {
            $this->pedido = new Pedido();    
        } 

        public function readAll($idCliente){
            return $this->pedido->readAll($idCliente);
        }

        public function adicionarPedido($usuario){
            return $this->pedido->adicionarPedido($usuario);
        }

        public function pedidoEntregue($usuario) {
            return $this->pedido->pedidoEntregue($usuario);
        }

        public function adicionarNota($idPedido, $nota){
            return $this->pedido->adicionarNota($idPedido, $nota);
        }
        
    }
?>