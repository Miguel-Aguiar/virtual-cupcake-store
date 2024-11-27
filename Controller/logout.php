<?php
session_start();

// Destroi todas as variáveis de sessão
session_unset();

// Destroi a sessão
session_destroy();

// Redireciona o usuário de volta para a página de login
header('Location: ../View/home.php');
exit();
?>
