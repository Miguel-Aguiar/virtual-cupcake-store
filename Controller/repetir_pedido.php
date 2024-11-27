<?php
    require_once('../Model/Database.php');
    require_once('../Controller/CarrinhoController.php');

    $data = json_decode(file_get_contents('php://input'), true);
    $pedido = $data['pedido'];

    try {

        $db = new Database();
        $conn = $db->connect();

        $carrinhoController = new CarrinhoController();
        $carrinhoController->repetirPedido($pedido);

        exit;
    } catch (Exception $e) {
        echo "Error : " . $e;
        exit;
    }

?>