function validarSenhas(event) {
    const senha = document.getElementById("password").value;
    const confirmarSenha = document.getElementById("confirm-password").value;

    if (senha !== confirmarSenha) {
        event.preventDefault();
        alert("As senhas não conferem. Por favor, tente novamente.");
        return false;
    }

    return true;
}

function verificarSenha(){
    const senha = document.getElementById('password');
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

function adicionarAoCarrinho(tipo, id, operacao) {

    const itemId = id;
    const tipoProduto = tipo;
    
    fetch('../Controller/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ itemId, tipoProduto, operacao })
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {
            if(data.message == 'adicionar'){
                exibirNotificacao();
            }
            atualizarCarrinho();
        } else {
            if(data.message == 'Você precisa estar logado para adicionar itens ao carrinho.'){
                exibirNotificacao('aviso');
            }else {
                alert('Erro ao adicionar o item ao carrinho. ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Erro: ', error);
    });
}

function adicionarAoCarrinhoVerificacao(tipo, id, operacao){
    adicionarAoCarrinho(tipo, id, operacao);
    location.reload();
}

function repetirPedido(idPedido){
    const pedido = idPedido;

    fetch('../Controller/repetir_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ pedido })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            
            data.data.forEach(item => {
                for (let i = 0; i < item.quantidade; i++) {
                    adicionarAoCarrinho(item.tipo, item.idItem);
                }
            });

        }else {
            alert('Erro ao repetir o pedido.' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro: ', error);
    })
}

function atualizarCarrinho() {
    
    fetch('../Controller/get_cart.php')
        .then(response => response.json())
        .then(data => {

            const cartPedidos = document.querySelector('.cart-pedidos');
            
            cartPedidos.innerHTML = '';

            let carrinho;

            if(document.querySelector('.resumo')){
                carrinho = document.querySelector('.resumo');
                carrinho.innerHTML = '';
            }
            
            if (data.success) {
                let valorTotal = 0;

                if(data.itens.length == 0){
                    location.reload();
                }
                
                carrinhoItensIterator = 0;
                i = 0;

                data.itens.forEach(item => {
                    
                    const itemElement = document.createElement('div');
                    itemElement.classList.add('cart-pedido');
                    
                    if(item.tipo == 'cupcake'){
                        
                        itemElement.innerHTML = `
                            <img src=./assets/cupcake${item.descricao}.png>
                            <span>R$ ${formatNumber(item.preco)} <br> ${item.descricao}</span>
                            <div class='qtd'>
                                <div onclick="adicionarAoCarrinho('${item.tipo}', ${item.id}, 'diminuir')">-</div>
                                <div class='center'>${item.quantidade}</div>
                                <div onclick="adicionarAoCarrinho('${item.tipo}', ${item.id}, 'adicionar')">+</div>
                            </div>
                        `;
                    }else if(item.tipo == 'combo'){

                        let sabor = item.sabores.split(', ')

                        itemElement.innerHTML = `
                        <div class="cart-pedido-combo">
                            <img src=./assets/cupcake${sabor[0]}.png>
                            <img src=./assets/cupcake${sabor[1]}.png>
                            <img src=./assets/cupcake${sabor[2]}.png>
                        </div>
                            <span>R$ ${formatNumber(item.preco)} <br> ${item.descricao}</span>
                            <div class='qtd'>
                                <div onclick="adicionarAoCarrinho('${item.tipo}', ${item.id}, 'diminuir')">-</div>
                                <div class='center'>${item.quantidade}</div>
                                <div onclick="adicionarAoCarrinho('${item.tipo}', ${item.id}, 'adicionar')">+</div>
                            </div>
                        `;
                        i++;
                    }

                    cartPedidos.appendChild(itemElement);
                    valorTotal += (item.preco * item.quantidade);

                    if(carrinho){
                        
                        const resumoItens = document.createElement('div');
                        resumoItens.classList.add('resumo-itens');

                        const sabor = document.createElement('span');
                        const preco = document.createElement('span');

                        if(item.tipo == 'combo'){
                            sabor.innerHTML = item.descricao;
                        }else {
                            sabor.innerHTML = 'Cupcake ' + item.descricao;
                        }
                        preco.innerHTML = 'R$ ' + formatNumber(item.preco);

                        const qtd = document.createElement('div');
                        qtd.classList.add('qtd');

                        if(item.quantidade == 1){
                            qtd.innerHTML = `
                                <div onclick=\"adicionarAoCarrinhoVerificacao('${item.tipo}', ${item.id}, 'diminuir')\">-</div>
                                <div class='center'>${item.quantidade}</div>
                                <div onclick=\"adicionarAoCarrinho('${item.tipo}', ${item.id}, 'adicionar')\">+</div>
                            `;
                        }else {
                            qtd.innerHTML = `
                                <div onclick=\"adicionarAoCarrinho('${item.tipo}', ${item.id}, 'diminuir')\">-</div>
                                <div class='center'>${item.quantidade}</div>
                                <div onclick=\"adicionarAoCarrinho('${item.tipo}', ${item.id}, 'adicionar')\">+</div>
                            `;
                        }
                        
                        resumoItens.appendChild(sabor);
                        resumoItens.appendChild(preco);
                        resumoItens.appendChild(qtd);
                        
                        carrinho.appendChild(resumoItens);

                        
                        const v = document.querySelector('.resumo-pedido-footer span');
                        v.innerHTML = `Valor total: R$ ${formatNumber(valorTotal)}`;


                        const carrinhoItens = document.querySelectorAll('.carrinho-itens-qtd-valor');
                        carrinhoItens[carrinhoItensIterator].innerHTML = '';

                        const itemQtd = document.createElement('small');
                        const itemValor = document.createElement('small');

                        if(item.tipo == 'cupcake')
                            itemQtd.innerHTML = "Quantidade: " + item.quantidade;
                        else if(item.tipo == 'combo')
                            itemQtd.innerHTML = "Quantidade: " + item.quantidade + " combo(s)";

                        itemValor.innerHTML = "Valor Total: R$ " + formatNumber(item.preco*item.quantidade);
                        
                        carrinhoItens[carrinhoItensIterator].appendChild(itemQtd);
                        carrinhoItens[carrinhoItensIterator].appendChild(itemValor);

                        carrinhoItensIterator++;
                    }
                });

                const p = document.querySelector('.cart-total');
                p.innerHTML = `Valor total: R$ ${formatNumber(valorTotal)}`;
                
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar carrinho: ', error);
        });
}

function adicionarPersonalizado(){

    const sabores = document.querySelectorAll('.selected');
    if(sabores.length != 3){
        exibirNotificacao('selecione-sabores');
        return;
    }

    const saborMassa = document.querySelector('.sabor.massa.selected').querySelector('span').innerHTML;
    const saborRecheio = document.querySelector('.sabor.recheio.selected').querySelector('span').innerHTML;
    const saborCobertura = document.querySelector('.sabor.cobertura.selected').querySelector('span').innerHTML;

    fetch('../Controller/add_personalizado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ saborMassa, saborRecheio, saborCobertura })
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {
            adicionarAoCarrinho('cupcake', data.idCupcake);
        } else {
            if(data.message = "Cupcake já existe no banco de dados."){                
                adicionarAoCarrinho('cupcake', data.idCupcake);
            }else {
                console.error("Erro ao adicionar cupcake personalizado: " + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Erro: ', error);
    });
}

function exibirNotificacao(tipo){
    const notification = document.getElementById('notification');

    switch (tipo) {
        case 'aviso':
            notification.innerHTML = 'O usuário precisa estar logado';
            notification.style.backgroundColor = "#d90000";
            break;
        case 'avaliacao':
            notification.innerHTML = 'Obrigado pela avaliação!';
            break;
        case 'selecione-sabores':
            notification.innerHTML = "Selecione os sabores!";
            notification.style.backgroundColor = "#d90000";
            break;
        case 'beneficio':
            notification.innerHTML = 'Benefício resgatado!';
            break;
        case 'metodo-pagamento':
            notification.innerHTML = 'Selecione um método de pagamento!'
            notification.style.backgroundColor = "#d90000";
            break;
        case 'endereco-incompleto':
            notification.innerHTML = 'Endereço incompleto'
            notification.style.backgroundColor = "#d90000";
            break;
        default:
            notification.innerHTML = "Item adicionado ao carrinho!";
            notification.style.backgroundColor = "#4CAF50";
            break;
    }

    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    cartSidebar.classList.toggle('show');
}

function selecionarSabor(element, tipo) {
    const saboresMassa = document.querySelectorAll('.sabor.massa');
    const saboresRecheio = document.querySelectorAll('.sabor.recheio');
    const saboresCobertura = document.querySelectorAll('.sabor.cobertura');
    
    switch(tipo){
        case 'massa':
            saboresMassa.forEach(sabor => sabor.classList.remove('selected'));
            break;
            
        case 'recheio':
            saboresRecheio.forEach(sabor => sabor.classList.remove('selected'));
            break;

        case 'cobertura':
            saboresCobertura.forEach(sabor => sabor.classList.remove('selected'));
            break;
    }

    element.classList.add('selected');
}

function selecionarMetodoPagamento(element, tipo) {
    const metodoPagamento = document.querySelectorAll('div.opcao');
    metodoPagamento.forEach(opcao => opcao.classList.remove('selected'));
    element.classList.add('selected')
    
    const credito = document.getElementById('credito');
    const pix = document.getElementById('pix');
    
    if(tipo == 'credito'){
        pix.classList.remove('show');

        credito.classList.toggle('show');
    } else if(tipo == 'pix'){
        credito.classList.remove('show');

        pix.classList.toggle('show');
    } else {
        pix.classList.remove('show');
        credito.classList.remove('show');
    }
}

function realizarPagamento(){

    const cep = document.getElementById("cepCarrinho").value;
    const endereco = document.getElementById("enderecoCarrinho").value;

    if(!(cep && endereco)){
        exibirNotificacao('endereco-incompleto');
        return;   
    }

    const selected = document.querySelector('.opcao.selected')
    
    if(selected){
        
        fetch('../Controller/add_pedido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            
            if (data.success) {
                location.href = "pedidos.php?acao=novo_pedido";
            } else {
                console.error('Erro ao registrar o pedido:', data.message);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
        });

    }else {
        exibirNotificacao('metodo-pagamento')
    }
}

function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

const acao = getQueryParam('acao');

if (acao === 'novo_pedido') {
    toggleSecao('acompanhar-pedido');
    limparUrl();
    
    previsaoEntrega();

    setTimeout(() => {
        document.querySelector('.barra-progresso-center').classList.add('fill-from-left');
    }, 5000);

    setTimeout(() => {
        toggleMapa();
    }, 10000);
    
    pedidoEntregue();
}


switch (acao) {
    case 'registro_feito':
        alert("O registro foi feito com sucesso! Faça login!");
        limparUrl();
        break;

    case 'email_ja_registrado':
        alert("O email já está registrado!");
        limparUrl();
        break;

    case 'email_nao_registrado':
        alert("O email não está registrado");
        limparUrl();
        break;

    case 'login_incorreto':
        alert("O email e/ou senha estão incorreto(s)");
        limparUrl();
        break;

    case 'pedido_avaliado':
        exibirNotificacao('avaliacao');
        limparUrl();
        break;
    case 'redefinir_erro':
        alert('E-mail ou número de celular inválidos.');
        limparUrl();
        break;
    case 'senha_alterada':
        alert('Senha alterada com sucesso.');
        limparUrl();
    default:
        break;
}

function toggleMapa(){
    document.querySelector('.barra-progresso-right').classList.add('fill-from-left');

    const mapa = document.getElementById('mapa')
    mapa.style.display = 'none';

    const confirmarEntrega = document.querySelector('.confirmar-entrega');
    confirmarEntrega.style.display = 'flex';
}

function enviarAvaliacao(){
    const stars = document.querySelectorAll('.star');
    let nota = 0;
    stars.forEach(star => {
        if(star.classList.contains('filled')){
            nota++;
        }
    });

    const idPedido = document.querySelector('div.idPedido').dataset.id;
    
    fetch('../Controller/add_nota.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ idPedido, nota })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.href = "pedidos.php?acao=pedido_avaliado";
        }else {
            alert('Erro');
        }
    })
    .catch(error => {
        console.error('Erro: ', error);
    })
}

function limparUrl() {
    window.history.replaceState(null, null, window.location.pathname);
}

function previsaoEntrega() {
    const agora = new Date();
    
    agora.setMinutes(agora.getMinutes() + 1);
    
    const horas = agora.getHours().toString().padStart(2, '0');
    const minutos = agora.getMinutes().toString().padStart(2, '0');
    const previsao = `${horas}:${minutos}`;
    
    const elementoPrevisao = document.querySelector('span.previsao');
    if (elementoPrevisao) {
        elementoPrevisao.textContent = `Previsão de entrega: ${previsao}`;
    }
}

function pedidoEntregue(){
    fetch('../Controller/pedido_entregue.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {

            document.querySelector('.highlight-item h2').innerHTML = 'Pedido chegou';
            
            const pedidoFooter = document.querySelector('.acompanhar-pedido-footer');
            pedidoFooter.innerHTML = '';

            const temp = document.createElement('a');

            temp.innerHTML = `
                <button class='acompanhar-pedido-footer-button' onclick=\"toggleSecao('confirmar-entrega-temp')\">Confirmar entrega</button>
            `;

            pedidoFooter.appendChild(temp);

        } else {
            console.error('Erro: ', data.message);
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
    });
}

function toggleSecao(secao) {
    let temp;
    switch(secao){
        case 'acompanhar-pedido':
            temp = document.getElementById('AcompanharPedido');
            esconderMain(temp);
            break;

        case 'confirmar-entrega':
            temp = document.getElementById('AvaliarPedido');
            document.getElementById('AcompanharPedido').classList.toggle("show");
            esconderMain(temp);
            break;
        case 'confirmar-entrega-temp':
            temp = document.getElementById('AvaliarPedido');
            esconderMain(temp);
            break;
        case 'fechar-avaliacao':
            temp = document.getElementById('AvaliarPedido');
            esconderMain(temp);
            break;
            
        case 'enviar-avaliacao':
            temp = document.getElementById('AvaliarPedido');
            esconderMain(temp);
            enviarAvaliacao();
            break;

        case 'fechar-pedido':
            temp = document.getElementById("FecharPedido");
            enderecoPrincipal();
            break;
        }
    
    temp.classList.toggle("show");
}

function esconderMain(show) {
    const main = document.querySelector('.main')
    
    if(!show.classList.contains('show')){
        main.style.display = 'none';
    } else {
        main.style.display = 'flex';
    }
}

function enderecoPrincipal(){
    const endereco = document.getElementById('enderecoCarrinho');
    
    fetch('../Controller/get_endereco.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {            
            endereco.value = data.endereco;
        } else {
            endereco.value = '';
        }
    })
    .catch(error => {
        console.error('Erro: ', error);
    });
    
}

function selecionarQualidade(element) {
    element.classList.toggle('selected')
}

function fillStar(starValue) {
    
    const stars = document.querySelectorAll('.star');
    
    stars.forEach(star => star.classList.remove('filled'));
    
    stars.forEach((star, index) => {
        if (index < starValue) {
            star.classList.add('filled');
        }
    });
}

function toggleMenu() {
    const menu = document.querySelector('ul.menu');
    menu.classList.toggle('active');
}

if(document.getElementById('Carrinho')){
    const carrinhoContainer = document.querySelector('.carrinho-itens');
    let scrollAmount = 0;
    let isScrolling = false;
    
    carrinhoContainer.addEventListener('wheel', (event) => {
        event.preventDefault();
        scrollAmount += event.deltaY * 0.1;
    
        if (!isScrolling) {
            smoothScroll();
        }
    });

    function smoothScroll() {
        isScrolling = true;
        scrollAmount *= 0.8;
        carrinhoContainer.scrollLeft += scrollAmount;
    
        if (Math.abs(scrollAmount) > 0.5) {
            requestAnimationFrame(smoothScroll);
        } else {
            isScrolling = false;
        }
    }
    
    function scrollar(lado) {
        const deslocamento = lado == 'left' ? -240 : 240;
        carrinhoContainer.scrollBy({
            left: deslocamento,
            behavior: 'smooth'
        });
    }
}


function formatNumber(value) {
    return new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
}