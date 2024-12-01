<?php
    include_once "../Controller/PedidoController.php";

    $data = json_decode(file_get_contents('php://input'), true);
    $idPedido = $data['idPedido'];
    $nota = $data['nota'];
    
    $pedido = new PedidoController();
    $pedido->adicionarNota($idPedido, $nota);
?>