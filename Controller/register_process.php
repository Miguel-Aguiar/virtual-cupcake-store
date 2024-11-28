<?php
require_once '../Model/Database.php';
require_once '../Controller/CarrinhoController.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];

    $sql_check_email = "SELECT * FROM cliente WHERE email = ?";
    $stmt = $conn->prepare($sql_check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        echo "Este e-mail já está registrado. Por favor, use outro.<br>";
        echo "<a href='../View/login.html'>Voltar</a>";
        exit;
    }

    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
    $confirmar_senha = password_hash($_POST['confirmar-senha'], PASSWORD_BCRYPT);
    
    $nome = $_POST['nome'] . ' ' . $_POST['sobrenome'];
    
    $celular = $_POST['phone'];
    $endereco = $_POST['endereco'];

    $restricoes = isset($_POST['restricoes']) ? $_POST['restricoes'] : [];
    
    $restricoes_string = implode(', ', $restricoes);

    $sql = "INSERT INTO cliente (nome, email, senha, endereco, numeroContato, restricoes) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
     $stmt->bind_param(
        "ssssss",
        $nome,
        $email,
        $senha,
        $endereco,
        $celular,
        $restricoes_string
    );

    if ($stmt->execute()) {
        
        $carrinho = new CarrinhoController();
        $carrinho->criarCarrinho($email);

        header('Location: ../View/login.html?acao=registro_feito');
        exit();

    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
