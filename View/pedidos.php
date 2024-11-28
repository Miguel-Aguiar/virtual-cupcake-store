<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja de Cupcakes</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">

</head>
<body id="Pedidos">

    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="home.php"><img src="./assets/logo.png" alt="Logo" width="96px" /></a>
            </div>
            <ul class="menu">
                <li><a href="home.php">Home</a></li>
                <li><a class="active" href="pedidos.php">Pedidos</a></li>
                <li><a href="combos.php">Combos</a></li>
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
                <span class="pag">Pedidos</span>
                <div class="main">
                    <?php 
                        include_once '../Controller/PedidoController.php';

                        $idCliente = '';
                        if(isset($_SESSION['usuario_id'])){
                            $idCliente = $_SESSION['usuario_id'];
                        }
                        
                        $controller = new PedidoController();                        
                        $pedidos = $controller->readAll($idCliente);

                        if(!$idCliente != ''){
                            echo "<h2 class='nao_logado'>O usuário deve estar logado para ter acesso aos pedidos!</h2>";
                        }else if($pedidos == null){
                            echo "<h2 class='nao_logado'>Você ainda não fez nenhum pedido!</h2>";
                        }else {
    
                            foreach ($pedidos as $pedido) {
                                $valorPedido = 0;
    
                                echo "<div class='highlight-item'>";
                                echo "<h2>";
                                echo $pedido['status'] == 'pendente' ? 'Em andamento' : 'Pedido entregue';
                                echo "</h2>";
                                echo "<div class='pedido-produtos'>";
    
                                if($pedido['cupcakes']){
                                    $cupcakes = explode('|', $pedido['cupcakes']);

                                    foreach ($cupcakes as $cupcake) {
                                        
                                        list($sabor, $quantidade, $preco) = explode(' ', $cupcake);
                                        echo "<div class='produto'>";
                                        echo "<span>" . $quantidade .  "x Cupcake " . htmlspecialchars($sabor) . "</span>";
                                        echo "<span>R$" . number_format($preco*$quantidade, 2, ',', '.') . "</span>";
                                        echo "</div>";
                                        $valorPedido += $preco*$quantidade;
                                    }
                                }

                                if($pedido['combos']){
                                    $combos = explode('|', $pedido['combos']);
                                    foreach ($combos as $combo) {
                                        list($tamanho, $quantidade, $preco) = explode(' ', $combo);
                                        $valorCombo = $preco * $quantidade;
                                        echo "<div class='produto'>";
                                        echo "<span>" . $quantidade . "x Combo " . htmlspecialchars($tamanho) . "</span>";
                                        echo "<span>R$" . number_format($valorCombo, 2, ',', '.') . "</span>";
                                        echo "</div>";
                                        $valorPedido += $preco*$quantidade;
                                    }
                                }
    
    
                                echo "</div>"; 
                                echo "<span>Valor Total: R$" . number_format($valorPedido, 2, ',', '.') . "</span>";
                                echo "<div class='acompanhar-pedido-footer'>";
                                
                                if($pedido['status'] == 'pendente') {
                                    echo "<button class='acompanhar-pedido-footer-button' onclick=\"toggleSecao('acompanhar-pedido')\">Acompanhar pedido</button>";
                                }else {
                                    echo "<a href='#' onclick=\"repetirPedido($pedido[idPedido])\"><img src='./assets/plus.png' width='50px'></a>";
                                }
    
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                        
                    ?>
                </div>
                
                <div id="AcompanharPedido" class="choose-flavor">
                    <img class="close" onclick="toggleSecao('acompanhar-pedido')" src="./assets/fechar.png" width="42px">
                    <div class="acompanhar-pedido">
                        <div class="pagamento">
                            <div>
                                <div class="barra-progresso">
                                    <div class="progresso barra-progresso-left"></div>
                                    <div class="progresso barra-progresso-center"></div>
                                    <div class="progresso barra-progresso-right"></div>
                                </div>
                                <div class="info">
                                    <span>Pagamento realizado</span>
                                    <span>Pedido sendo preparado</span>
                                    <span>Pedido a caminho</span>
                                </div>
                            </div>
                            <span class="previsao">Previsão de entrega - 20:35</span>
                        </div>
                        <div class="mapa" id="mapa">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14630.85049166879!2d-46.6328689!3d-23.542836349999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce58560a6c5f29%3A0xeff177e6d6a04b7a!2sCentro%20Hist%C3%B3rico%20de%20S%C3%A3o%20Paulo%2C%20S%C3%A3o%20Paulo%20-%20SP!5e0!3m2!1spt-BR!2sbr!4v1730840815028!5m2!1spt-BR!2sbr" 
                                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="confirmar-entrega">
                            <h2>O pedido chegou ao destino</h2>
                            <button onclick="toggleSecao('confirmar-entrega')">Confirmar entrega</button>
                        </div>
                    </div>
                </div>
                
                <div id="AvaliarPedido" class="choose-flavor avaliar">
                    <div class="avaliar-pedido">
                        <img class="close" onclick="toggleSecao('fechar-avaliacao')" src="./assets/fechar.png" width="42px">
                        <h2>Avalise seu pedido</h2>
                        <div class="avaliar">
                            <p>O que você achou do pedido ?</p>
                            <small>Escolha de 1 a 5 estrelas para avaliar!</small>
                        </div>
                        <div class="estrelas">
                            <span onclick="fillStar(1)" class="star filled" data-value="1">&#9733;</span>
                            <span onclick="fillStar(2)" class="star filled" data-value="2">&#9733;</span>
                            <span onclick="fillStar(3)" class="star filled" data-value="3">&#9733;</span>
                            <span onclick="fillStar(4)" class="star filled" data-value="4">&#9733;</span>
                            <span onclick="fillStar(5)" class="star filled" data-value="5">&#9733;</span>
                        </div>
                        <p>O que você gostou ?</p>
                        <div class="qualidades">
                            <div>
                                <span onclick="selecionarQualidade(this)">Saboroso</span>
                                <span onclick="selecionarQualidade(this)">Entrega rápida</span>
                                <span onclick="selecionarQualidade(this)">Boa quantidade</span>
                            </div>
                            <div>
                                <span onclick="selecionarQualidade(this)">Boa aparência</span>
                                <span onclick="selecionarQualidade(this)">Boa embalagem</span>
                                <span onclick="selecionarQualidade(this)">Bons ingredientes</span>
                            </div>            
                        </div>
                        <textarea maxlength="150"></textarea>
                        <button onclick="toggleSecao('enviar-avaliacao')">Enviar avaliação</button>
                    </div>
                </div>
                <div id="notification" class="notification">Obrigado pela avaliação!</div>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <p>&copy; 2024 Loja de Cupcakes. Todos os direitos reservados.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
