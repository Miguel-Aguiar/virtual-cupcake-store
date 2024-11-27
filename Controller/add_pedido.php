<?php
    include_once "../Controller/PedidoController.php";

    session_start();
    
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'message' => 'Você precisa estar logado.']);
        exit;
    }

    $usuario = $_SESSION['usuario_id']; 
    
    $pedido = new PedidoController();
    return $pedido->adicionarPedido($usuario);
?>