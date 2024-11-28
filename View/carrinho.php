<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja de Cupcakes</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">

</head>
<body id="Carrinho">

    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="home.php"><img src="./assets/logo.png" alt="Logo" width="96px" /></a>
            </div>
            <ul class="menu">
                <li><a href="home.php">Home</a></li>
                <li><a href="pedidos.php">Pedidos</a></li>
                <li><a href="combos.php">Combos</a></li>
                <li><a href="criarCupcake.php">Crie seu Cupcake</a></li>
                <li><a class="active" href="carrinho.php">Carrinho</a></li>
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
                <span class="pag">Carrinho</span>
                <?php

                    include_once "../Controller/CupcakeController.php";
                    include_once "../Controller/ComboController.php";

                    if(!isset($_SESSION['usuario_id'])){
                        echo "<h2 class='nao_logado'>O usuário deve estar logado para ter acesso ao carrinho!</h2>";
                        
                    }else if($carrinho == null){
                        echo "<h2 class='nao_logado'>O carrinho está vazio!</h2>";
                        
                    }else {
                        $cupcakeController = new Cupcake();
                        $comboController = new Combo();
                        
                        echo "
                            <span onclick=\"scrollar('left')\" class='arrow left'>&lt;</span>
                            <span onclick=\"scrollar('right')\" class='arrow right'>&gt;</span>
                        ";

                        echo "<div class='carrinho-itens'>";
                        
                        $primeiroItem = reset($carrinho);
                        $ultimoItem = end($carrinho);

                        foreach ($carrinho as $item) {

                            if($item['tipo'] == 'cupcake'){
                                $restricao = $cupcakeController->verificarRestricoes($item['restricoes'], $userRestricoes);
                                if($restricao) $restricaoComum = "<small style='color:red;'>Contém: $restricao</small>";
                                else $restricaoComum = '';

                                $descricao = $cupcakeController->getDescricao($item['id']);
                                echo "<div class='highlight-item'>";
                                echo "<div class='sabores'>
                                            <img src='./assets/cupcake$item[sabor].png'>
                                        </div>
                                        <div>
                                            <span>R$ ". number_format($item['preco'], 2, ',', '.') . "</span>
                                            <br><span>$item[sabor]</span><br>
                                            <small class='carrinho-item-descricao'>$descricao</small>
                                            <br>$restricaoComum
                                        </div>
                                        <div class ='carrinho-itens-qtd-valor'>
                                            <small>Quantidade: $item[quantidade]</small>
                                            <small>Valor Total: R$ " . number_format($item['preco']*$item['quantidade'], 2, ',', '.') . "</small>
                                        </div>
                                    </div>
                                ";
                            }else if($item['tipo'] == 'combo'){

                                
                                $combo = $comboController->readCombo($item['id']);

                                $restricao = $cupcakeController->verificarRestricoes($combo['restricoes'], $userRestricoes);
                                if($restricao) $restricaoComum = "<small style='color:red;'>Contém: $restricao</small>";
                                else $restricaoComum = '';

                                $precos = explode(',', $combo['precosCupcakes']);
                                $sabores = explode(', ', $combo['sabores']);

                                $tamanho = strtolower(trim($combo['tamanho']));
                                echo "
                                    <div class='highlight-item highlight-item-combo'>
                                        <div class='sabores'>
                                            <img src='./assets/cupcake$sabores[0].png'>
                                            <img src='./assets/cupcake$sabores[1].png'>
                                            <img src='./assets/cupcake$sabores[2].png'>
                                        </div>
                                        <div class='combos-desc'>
                                            <span>Combo $combo[tamanho]</span>
                                            <span>" . ($tamanho == 'pequeno' ? '9' : ($tamanho == 'médio' ? '15' : '30')) . "x cupcakes</span>
                                        </div>";

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
                                    
                                    $fator = $tamanho == 'pequeno' ? 3 : ($tamanho == 'médio' ? 5 : 10);
                                    $desconto = $tamanho == 'pequeno' ? 0.05 : ($tamanho == 'médio' ? 0.10 : 0.15);
                                    $valorComFator = $valorTotal * $fator;
                                    $valorFinal = $valorComFator - ($valorComFator * $desconto);
                                    
                                    echo "<div class ='carrinho-itens-qtd-valor'>";
                                    echo "<small>Quantidade: $item[quantidade] combo(s)</small>";
                                    echo "<small>Valor Total: R$ " . number_format($valorFinal*$item['quantidade'], 2, ',', '.') . "</small>";
                                    echo "</div>";

                                    echo "<small class='desconto'>" . 
                                        ($tamanho == 'pequeno' ? '5' : ($tamanho == 'médio' ? '10' : '15'))
                                        . "% de desconto</small>";
                                    echo "</div>";
                            }
                        }
                        echo "</div>";
                        echo "
                            <div class='resumo-carrinho highlight-item'>
                                <span class='pag'>Resumo do Pedido</span>
                                <div class='resumo'>
                        ";

                        $valorTotal = 0;

                        foreach($carrinho as $item){

                            $titulo = $item['tipo'] == 'cupcake' ? 'Cupcake ' . $item['sabor'] : 'Combo ' . $item['tamanho'];

                            echo "
                                <div class='resumo-itens'>
                                    <span>" . $titulo . "</span>
                                    <span>R$ " . number_format($item['preco'], 2, ',', '.') . "</span>
                                    <div class='qtd'>";
                                    
                                    if($item['quantidade'] == 1){
                                        echo "<div onclick=\"adicionarAoCarrinhoVerificacao('$item[tipo]', $item[id], 'diminuir')\">-</div>";
                                    }else {
                                        echo "<div onclick=\"adicionarAoCarrinho('$item[tipo]', $item[id], 'diminuir')\">-</div>";
                                    }

                                    echo "
                                        <div class='center'>$item[quantidade]</div>
                                        <div onclick=\"adicionarAoCarrinho('$item[tipo]', $item[id], 'adicionar')\">+</div>
                                    </div>
                                </div>
                            ";
                            $valorTotal += $item['preco'] * $item['quantidade'];
                        }
                        
                        echo "</div>";
                        echo "
                            <div class='resumo-pedido-footer'>
                                <span>Valor total: R$ " . number_format($valorTotal, 2, ',', '.') . "</span>
                                <button onclick=\"toggleSecao('fechar-pedido')\">Fechar pedido</button>
                            </div>
                        ";
                        echo "</div>";
                        echo "</div>";
                    }
                
                ?>

                <div id="FecharPedido" class="fechar-pedido">
                    <img class="close" onclick="toggleSecao('fechar-pedido')" src="./assets/fechar.png" width="42px">
                    <div class="fechar-pedido-passos">
                        <h1>Endereço</h1>
                        <form>
                            <select name="enderecoSelect" id="enderecoSelect">
                                <option value="Endereço principal">Endereço principal</option>
                                <option value="Outro">Outro</option>
                            </select>
                    
                            <label for="cep">CEP*:</label>
                            <input type="text" id="cepCarrinho" name="cep" placeholder="00000-000" maxlength="9" pattern="\d{5}-?\d{3}" required>
                    
                            <label for="endereco">Endereço*:</label>
                            <input type="text" id="enderecoCarrinho" name="endereco" placeholder="Rua Exemplo, Av. Exemplo" required>
                    
                            <label for="numero">Número*:</label>
                            <input type="number" id="numeroCarrinho" name="numero" placeholder="555" required>
                            
                            <label for="complemento">Complemento:</label>
                            <input type="text" id="complemento" name="complemento" placeholder="Apto, bloco, etc.">
                              
                        </form>
                    </div>
                    <div class="fechar-pedido-passos center">
                        <h1>Forma de Pagamento</h1>
                        <div class="opcao" onclick="selecionarMetodoPagamento(this, 'credito')">
                            <img src="./assets/cartao.png">
                            <span>Cartão de crédito</span>
                            <div id="credito">
                                <form action="#">
                                    
                                    <input type="text" name="nome" placeholder="Nome do Titular" disabled>

                                    
                                    <input type="text" name="numero-cartao" placeholder="Número do Cartão" disabled>

                                    
                                    <input type="text" name="validade" placeholder="Validade (MM/AA)" disabled>

                                    
                                    <input type="text" name="codigo" placeholder="Código de Segurança" disabled>

                                    
                                    <input type="text" name="cpf" placeholder="CPF do Titular" disabled>
                                </form>
                            </div>
                        </div>
                        <div class="opcao" onclick="selecionarMetodoPagamento(this, 'pix')">
                            <img src="./assets/pix.png">
                            <span>Pix</span>
                            <div id="pix">
                                <img src="./assets/qrcode.png" width="150px">
                            </div>
                        </div>
                        <div class="opcao" onclick="selecionarMetodoPagamento(this, 'dinheiro')">
                            <img src="./assets/dinheiro.png">
                            <span>Dinheiro</span>
                        </div>
                        <button onclick="realizarPagamento()">Realizar pagamento</button>
                    </div>
                </div>
                <div id="notification" class="notification">Item adicionado ao carrinho!</div>
            </div>
        </section>
    </main>
    <footer class="footer">
        <p>&copy; 2024 Loja de Cupcakes. Todos os direitos reservados.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
