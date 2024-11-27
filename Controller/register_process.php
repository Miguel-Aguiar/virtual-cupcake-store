<?php
require_once '../Model/Database.php'; // Inclua a conexão com o banco de dados
require_once'../Controller/CarrinhoController.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];

    //Verificar email
    $sql_check_email = "SELECT * FROM cliente WHERE email = ?";
    $stmt = $conn->prepare($sql_check_email);
    $stmt->bind_param("s", $email); // Bind o e-mail para evitar SQL Injection
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email já está em uso
        echo "Este e-mail já está registrado. Por favor, use outro.<br>";
        echo "<a href='../View/login.html'>Voltar</a>";
        exit;
    }

    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Hash da senha
    $confirmar_senha = password_hash($_POST['confirmar-senha'], PASSWORD_BCRYPT);
    
    $nome = $_POST['nome'] . ' ' . $_POST['sobrenome'];
    // $sobrenome = $_POST['sobrenome'];
    $celular = $_POST['phone'];
    $endereco = $_POST['endereco'];

    $restricoes = isset($_POST['restricoes']) ? $_POST['restricoes'] : [];
    // Converter o array em uma string separada por vírgulas
    $restricoes_string = implode(', ', $restricoes);

    $sql = "INSERT INTO cliente (nome, email, senha, endereco, numeroContato, restricoes) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
     $stmt->bind_param(
        "ssssss", // Define o tipo de cada valor
        $nome,
        $email,
        $senha,
        $endereco,
        $celular,
        $restricoes_string
    );

    if ($stmt->execute()) {
        echo "Registro bem-sucedido! <a href='../View/login.html'>Faça login</a>";

        //Criar carrinho do cliente
        $carrinho = new CarrinhoController();
        $carrinho->criarCarrinho($email);
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
