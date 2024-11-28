<?php
    require_once('../Model/Database.php');
    require_once('../Controller/CupcakeController.php');

    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'message' => 'Você precisa estar logado.']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $massa = $data['saborMassa'];
    $recheio = $data['saborRecheio'];
    $cobertura = $data['saborCobertura'];

    try {

        $db = new Database();
        $conn = $db->connect();

        $cupcakeController = new CupcakeController();
        $cupcakeController->adicionarPersonalizado($massa, $recheio, $cobertura);

        echo json_encode([
            'success' => true,
            'message' => 'Item adicionado ao carrinho com sucesso!',
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }

?>