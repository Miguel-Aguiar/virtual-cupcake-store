<?php
    require_once('../Model/Database.php');
    require_once('../Controller/CupcakeController.php');
    require_once('../Controller/CarrinhoController.php');
    
    session_start();
    
    // Verificar se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        // echo json_encode(['success' => false, 'message' => 'Você precisa estar logado para adicionar ao carrinho.']);
        exit;
    }
    
    // Decodificar os dados enviados pelo fetch
    $data = json_decode(file_get_contents('php://input'), true);
    $itemId = $data['itemId'];
    $tipoProduto = $data['tipoProduto'];
    $operacao = $data['operacao'] ?? 'adicionar';
    try {
        // Instanciar o controlador de cupcakes
        $db = new Database();
        $conn = $db->connect();
    
        // Adicionar ao carrinho
        $carrinhoController = new CarrinhoController();
        $userId = $_SESSION['usuario_id'];
        $carrinhoController->adicionarAoCarrinho($userId, $itemId, $tipoProduto, $operacao);
    
        // echo json_encode(['success' => true, 'message' => 'Cupcake adicionado ao carrinho.']);
        exit;
    } catch (Exception $e) {
        // echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        exit;
    }
    
?>