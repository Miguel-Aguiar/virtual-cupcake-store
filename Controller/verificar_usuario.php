<?php
include_once '../Controller/ClienteController.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];

    $cliente = new ClienteController();

    $id = $cliente->redefinirSenha($email, $telefone);

    echo '
    <form action="atualizar_senha.php" method="POST" class="register-form">
        <input type="hidden" name="user_id" value="' . $id . '">
        <label for="nova_senha">Nova senha:</label>
        <input type="password" name="nova_senha" id="password" required>
        <button type="submit" id="submitBtn" disabled>Alterar senha</button>
    </form>';

}
?>

<script>
    const senha = document.getElementById('password');
    
    function verificarSenha(){
        const submitButton = document.getElementById('submitBtn');
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
        
        if (!regex.test(senha.value)) {
            senha.style.borderColor = 'red';
            submitButton.disabled = true;
        } else {
            senha.style.borderColor = 'lightgreen';
            submitButton.disabled = false;
        }
    }

    senha.addEventListener('blur', verificarSenha);

</script>