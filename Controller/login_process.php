<?php
session_start(); // Inicia a sessão

require_once '../Model/Database.php'; // Classe de conexão com o banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Conectar ao banco
    $db = new Database();
    $conn = $db->connect();

    // Preparar a consulta para buscar o usuário pelo e-mail
    $sql = "SELECT * FROM cliente WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o e-mail existe no banco de dados
    if ($result->num_rows > 0) {
        // Obtém os dados do usuário
        $usuario = $result->fetch_assoc();

        // Verifica se a senha fornecida corresponde à senha armazenada (verificando o hash)
        if (password_verify($senha, $usuario['senha'])) {
            // print_r($usuario);
            // Senha correta, inicia a sessão e redireciona para a página principal
            $_SESSION['usuario_id'] = $usuario['idCliente'];  // Armazena o ID do usuário na sessão
            $_SESSION['usuario_nome'] = $usuario['nome']; // Armazena o nome do usuário
            $_SESSION['usuario_restricoes'] = $usuario['restricoes'];
            header('Location: ../View/home.php');  
            // Redireciona para o painel (página protegida)
            exit();
        } else {
            // Senha incorreta
            echo "Senha incorreta.";
            echo "<a href='../View/login.html'>Voltar</a>";
        }
    } else {
        // E-mail não encontrado
        echo "E-mail não registrado.";
        echo "<a href='../View/login.html'>Voltar</a>";
    }

    $stmt->close();
    $conn->close();
}
?>
