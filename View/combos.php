<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja de Cupcakes</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">

    <script src="script.js"></script>

</head>
<body id="Combos">

    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="home.php"><img src="./assets/logo.png" alt="Logo" width="96px" /></a>
            </div>
            <ul class="menu">
                <li><a href="home.php">Home</a></li>
                <li><a href="pedidos.php">Pedidos</a></li>
                <li><a class="active" href="combos.php">Combos</a></li>
                <li><a href="criarCupcake.php">Crie seu Cupcake</a></li>
                <li><a href="carrinho.php">Carrinho</a></li>
                <li><a href="contato.php">Contato</a></li>
            </ul>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            <div class="account-area">
                <div class="cart-icon">
                    <a href="#" onclick="toggleCart()" ><img src="./assets/carrinhop.png" /></a>
                </div>
                <?php
                    session_start();

                    $userRestricoes = '';
                    if(isset($_SESSION['usuario_restricoes'])){
                        $userRestricoes = $_SESSION['usuario_restricoes'];
                    }

                    if (isset($_SESSION['usuario_id'])) {

                        $usuario_nome = $_SESSION['usuario_nome'];
                        echo "<a href='../Controller/logout.php' class='login-link'>Logout</a>";
                    } else {
                        echo "<a href='login.html' class='login-link'>Criar conta / Login</a>";
                    }
                ?>
            </div>
        </nav>
    </header>
    
    <div id="cart-sidebar" class="cart-sidebar">
        <h2>Resumo do Carrinho</h2>
        <img class="close" onclick="toggleCart()" src="./assets/fechar.png" width="42px">
        
        <?php
        include_once '../Controller/CarrinhoController.php';
        include_once '../Controller/ComboController.php';

        echo "<div class='cart-pedidos'>";
        if (isset($_SESSION['usuario_id'])) {
            $userId = $_SESSION['usuario_id'];

            $carrinhoController = new CarrinhoController();
            $carrinho = $carrinhoController->exibirCarrinho($userId);

            $comboController = new Combo();

            $valorTotal = 0;
            if ($carrinho) {
                foreach ($carrinho as $item) {
                    $tipo = $item['tipo'];
                    $id = $item['id'];
                    $preco = number_format($item['preco'], 2, ',', '.');
                    $quantidade = $item['quantidade'];

                    if($tipo == 'cupcake'){
                        $sabor = htmlspecialchars($item['sabor'], ENT_QUOTES, 'UTF-8');
                    }else if($tipo == 'combo'){
                        $tamanho = $item['tamanho'];
                    }

                    $imagem = $tipo === 'cupcake' ? "./assets/cupcake$sabor.png" : "./assets/cupcake";
                    
                    if($tipo == 'cupcake'){

                        echo "<div class='cart-pedido'>
                            <img src='$imagem'>
                            <span>R$ $preco <br> $sabor</span>
                            <div class='qtd'>
                                <div onclick=\"adicionarAoCarrinho('$tipo', $id, 'diminuir')\">-</div>
                                <div class='center'>$quantidade</div>
                                <div onclick=\"adicionarAoCarrinho('$tipo', $id, 'adicionar')\">+</div>
                            </div>
                        </div>";
                    }else if ($tipo == 'combo'){
                        $combo = $comboController->readCombo($item['id']);
                        $sabores = explode(', ', $combo['sabores']);
                        
                        echo "<div class='cart-pedido'>
                            <div class='cart-pedido-combo'>
                                <img src='$imagem$sabores[0].png' width='24px'>
                                <img src='$imagem$sabores[1].png' width='24px'>
                                <img src='$imagem$sabores[2].png' width='24px'>
                            </div>
                            <span>R$ $preco <br> Combo $tamanho</span>
                            <div class='qtd'>
                                <div onclick=\"adicionarAoCarrinho('$tipo', $id, 'diminuir')\">-</div>
                                <div class='center'>$quantidade</div>
                                <div onclick=\"adicionarAoCarrinho('$tipo', $id, 'adicionar')\">+</div>
                            </div>
                        </div>";
                    }


                    $valorTotal += $item['preco'] * $item['quantidade'];
                }
                echo "</div>";

                echo "
                <p class='cart-total'>Valor total: R$ " . number_format($valorTotal, 2, ',', '.') . "</p>
                <a href='carrinho.php'><button>Ir para o carrinho</button></a>";
            } else {
                echo "
                    O carrinho está vazio
                    </div>
                    <p class='cart-total'>Valor total: R$ " . number_format($valorTotal, 2, ',', '.') . "</p>
                    <a href='carrinho.php'><button>Ir para o carrinho</button></a>
                ";
            }
        } else {
            echo "</div>";
            echo 'O usuário deve logar para ter acesso ao carrinho!';
        }
        ?>
        
    </div>
    <main>
        <section class="highlight">
            <div class="highlight-container">
                <span class="pag">Combos</span>

                <div class="main">
                    <?php 
                        include_once '../Controller/ComboController.php';
                        include_once '../Controller/CupcakeController.php';

                        $controller = new ComboController();                        
                        $combos = $controller->readAll();
                        $cupcakeController = new CupcakeController();

                        
                        foreach ($combos as $combo) {

                            $restricao = $cupcakeController->verificarRestricoes($combo['restricoes'], $userRestricoes);
                            if($restricao) $restricaoComum = "<small style='color:red;'>Contém: $restricao</small>";
                            else $restricaoComum = '';

                            $precos = explode(',', $combo['precosCupcakes']);
                            $sabores = explode(', ', $combo['sabores']);
                            echo "<div class='highlight-item'";
                            echo "<h3>" . $combo['tamanho'] . "</h3>";

                            echo "
                                <div class='sabores'>
                                <img src='./assets/cupcake$sabores[0].png'>
                                    <img src='./assets/cupcake$sabores[1].png'>
                                    <img src='./assets/cupcake$sabores[2].png'>
                                </div>
                            ";
                            
                            echo "<div class='combos-desc'>";
                            $tamanho = strtolower(trim($combo['tamanho']));
                            echo "<span>" . ($tamanho == 'pequeno' ? '9' : ($tamanho == 'médio' ? '15' : '30')) . "x Cupcakes</span>";
                            echo "</div>";

                            echo "<div class='combos-sabores'>";
                            
                            
                            
                            $valorTotal = 0;
                            for ($i=0; $i < 3; $i++) { 
                                echo "
                                    <div class='sabor'>
                                        <span>R$ " . number_format($precos[$i], 2, ',', '.') . "</span>
                                        <span> " . $sabores[$i] . " </span>
                                    </div>
                                ";
                                $valorTotal += $precos[$i];
                            }
                            
                            echo "</div>";
                            echo $restricaoComum;

                            echo "<div class='combos-button'>
                                    <button onclick=\"adicionarAoCarrinho('combo', " . $combo['idCombo'] . ")\">Adicionar ao Carrinho</button>
                                </div>";
                            $fator = $tamanho == 'pequeno' ? 3 : ($tamanho == 'médio' ? 5 : 10);
                            $desconto = $tamanho == 'pequeno' ? 0.05 : ($tamanho == 'médio' ? 0.10 : 0.15);
                            $valorComFator = $valorTotal * $fator;
                            $valorFinal = $valorComFator - ($valorComFator * $desconto);

                            echo "<small>Valor Total: R$ " . number_format($valorFinal, 2, ',', '.') . "</small>";

                            echo "<small class='desconto'>" . 
                                ($tamanho == 'pequeno' ? '5' : ($tamanho == 'médio' ? '10' : '15'))
                                . "% de desconto</small>";
                            echo "</div>";
                        }
                    ?>
                </div>
                <div id="notification" class="notification">Item adicionado ao carrinho!</div>
            </div>

        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 Loja de Cupcakes. Todos os direitos reservados.</p>
    </footer>    
</body>
</html>
