<?php
    require_once('../Model/Database.php');
    require_once('../Controller/CupcakeController.php');
    require_once('../Controller/CarrinhoController.php');
    
    session_start();
    
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'message' => 'Você precisa estar logado para adicionar itens ao carrinho.']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $itemId = $data['itemId'];
    $tipoProduto = $data['tipoProduto'];
    $operacao = $data['operacao'] ?? 'adicionar';
    try {

        $db = new Database();
        $conn = $db->connect();
    
        
        $carrinhoController = new CarrinhoController();
        $userId = $_SESSION['usuario_id'];
        $carrinhoController->adicionarAoCarrinho($userId, $itemId, $tipoProduto, $operacao);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: aqui' . $e->getMessage()]);
        exit;
    }
    
?>