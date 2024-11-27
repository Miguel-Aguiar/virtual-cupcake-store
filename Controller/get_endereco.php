<?php
    include_once "../Controller/ClienteController.php";

    session_start();
    $usuario = $_SESSION['usuario_id'];

    $cliente = new ClienteController();
    $cliente->getEndereco($usuario);

?>