<?php
include_once '../Controller/ClienteController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
    $user_id = $_POST['user_id'];

    $cliente = new ClienteController();
    $cliente->atualizarSenha($nova_senha, $user_id);

}
?>
