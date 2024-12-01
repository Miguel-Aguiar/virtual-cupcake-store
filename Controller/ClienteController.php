<?php
    require_once '../Model/ClienteModel.php';

    class ClienteController {

        private $cliente;

        public function __construct() {
            $this->cliente = new Cliente();
        }

        public function getEndereco($usuario){
            return $this->cliente->getEndereco($usuario);
        }

        public function redefinirSenha($email, $telefone){
            return $this->cliente->redefinirSenha($email, $telefone);
        }

        public function atualizarSenha($nova_senha, $user_id){
            $this->cliente->atualizarSenha($nova_senha, $user_id);
        }

    }
?>