<?php
    require_once '../Model/ClienteModel.php';

    class ClienteController {

        public function getEndereco($usuario){
            $cliente = new Cliente();
            return $cliente->getEndereco($usuario);
        }

    }
?>