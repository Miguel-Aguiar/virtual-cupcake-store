<?php
include_once '../Model/Database.php'; // Inclua o arquivo de conexão com o banco de dados

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Você precisa estar logado.']);
    exit;
}

$userId = $_SESSION['usuario_id'];

$db = new Database();
$conn = $db->connect();

// Consulta para pegar os itens do carrinho
$sql = "SELECT
        'cupcake' AS tipo,
        c.idCupcake AS id,
        c.quantidade,
        cp.sabor AS sabor,
        cp.preco
    FROM 
        carrinho_cupcake c
    JOIN 
        cupcake cp 
    ON 
        c.idCupcake = cp.idCupcake
    WHERE 
        c.idCarrinho = (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)

    UNION ALL

    SELECT 
        'combo' AS tipo,
        cb.idCombo AS id,
        cb.quantidade,
        CONCAT('Combo ', co.tamanho) AS descricao,
        co.preco
    FROM 
        carrinho_combo cb
    JOIN 
        combo co 
    ON 
        cb.idCombo = co.idCombo
    WHERE 
        cb.idCarrinho = (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$itens = [];
while ($row = $result->fetch_assoc()) {
    $itens[] = $row;
}

echo json_encode(['success' => true, 'itens' => $itens]);
exit;
?>