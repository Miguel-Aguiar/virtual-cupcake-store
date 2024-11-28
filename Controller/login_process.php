<?php
session_start();

require_once '../Model/Database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    
    $db = new Database();
    $conn = $db->connect();

    
    $sql = "SELECT * FROM cliente WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        
        $usuario = $result->fetch_assoc();

        
        if (password_verify($senha, $usuario['senha'])) {
            
            $_SESSION['usuario_id'] = $usuario['idCliente'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_restricoes'] = $usuario['restricoes'];
            header('Location: ../View/home.php');  
            
            exit();
        } else {
            
            header('Location: ../View/login.html?acao=login_incorreto');
            exit();
            
        }
    } else {
        
        header('Location: ../View/login.html?acao=email_nao_registrado');
        exit();
        
    }

    $stmt->close();
    $conn->close();
}
?>
