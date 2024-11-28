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

    }
?>