<?php
include_once '../Controller/CarrinhoController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Você precisa estar logado.']);
    exit;
}

$userId = $_SESSION['usuario_id'];

$carrinho = new CarrinhoController();
$carrinho->getCart($userId);

?>