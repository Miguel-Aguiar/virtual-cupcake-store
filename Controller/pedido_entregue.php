<?php
    require_once('../Model/Database.php');
    require_once('../Controller/PedidoController.php');

    session_start();
    $usuario = $_SESSION['usuario_id'];

    try {

        $pedidoController = new PedidoController();
        $pedidoController->pedidoEntregue($usuario);

    } catch (Exception $e) {
        echo "Error : " . $e;
        exit;
    }

?>