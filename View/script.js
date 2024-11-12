function adicionarAoCarrinho() {
    // Lógica para adicionar ao carrinho

    exibirNotificacao();
}

function exibirNotificacao(){
    const notification = document.getElementById('notification');
    
    const currentPath = window.location.pathname;
    if(currentPath == "/View/criarCupcake.html"){
        const sabores = document.querySelectorAll('.selected');
        if(sabores.length != 3){

            notification.innerHTML = "Selecione os sabores!"
            notification.style.backgroundColor = "#d90000"
            // return;
        } else {
            notification.innerHTML = "Item adicionado ao carrinho!";
            notification.style.backgroundColor = "#4CAF50"
        }
    }

    // Mostra a notificação
    notification.classList.add('show');
    
    // Oculta a notificação após 3 segundos
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Mostra o mini carrinho
function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    cartSidebar.classList.toggle('show');
}

// Seleciona sabor no criar cupcake
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

// Scroll horizontal 
const carrinhoContainer = document.querySelector('.carrinho-itens');
let scrollAmount = 0; // Armazena o valor da rolagem a ser aplicado
let isScrolling = false; // Verifica se já está rolando

carrinhoContainer.addEventListener('wheel', (event) => {
    event.preventDefault();
    scrollAmount += event.deltaY * 0.1; // Ajuste a multiplicação para alterar a velocidade (0.3 é o fator de suavização)

    if (!isScrolling) {
        smoothScroll();
    }
});

function smoothScroll() {
    isScrolling = true;
    // Reduz gradualmente o valor de scrollAmount para um efeito de suavização
    scrollAmount *= 0.8;
    carrinhoContainer.scrollLeft += scrollAmount;

    // Continua rolando enquanto o valor absoluto de scrollAmount for significativo
    if (Math.abs(scrollAmount) > 0.5) {
        requestAnimationFrame(smoothScroll);
    } else {
        isScrolling = false; // Para a animação quando quase não houver movimento
    }
}

function scrollar(lado) {
    const deslocamento = lado == 'left' ? -240 : 240;
    carrinhoContainer.scrollBy({
        left: deslocamento,
        behavior: 'smooth'
    });
}

// Selecionar método de pagamento
function selecionarMetodoPagamento(element, tipo) {
    const metodoPagamento = document.querySelectorAll('div.opcao');
    metodoPagamento.forEach(opcao => opcao.classList.remove('selected'));
    element.classList.add('selected')
    
    if(tipo == 'credito'){
        // formulario desativado
    } else if(tipo == 'pix'){
        //abrir popup esperar 3 sec dar pagamento confirmado 

    } else {
        // dinhero, nao precisa fazer nada
    }
}

// Realizar pagamento
function realizarPagamento() {
    window.location.href = "http://127.0.0.1:5500/View/pedidos.html";
}

//Acompanhar pedido
function acompanharPedido() {
    const acompanharPedido = document.getElementById('AcompanharPedido');
    acompanharPedido.classList.toggle('show');
    
    esconderMain(acompanharPedido);
}

function confirmarEntrega() {
    acompanharPedido();
    const avaliarPedido = document.getElementById('AvaliarPedido');
    avaliarPedido.classList.toggle("show");

    esconderMain(avaliarPedido);
}

// Enviar avaliação
function enviarAvaliacao(){
    exibirNotificacao();
    //Lógica para guardar avaliação
}

function toggleSecao(secao) {
    let temp;
    switch(secao){
        case 'acompanhar-pedido':
            temp = document.getElementById('AcompanharPedido');
            break;

        case 'confirmar-entrega':
            temp = document.getElementById('AvaliarPedido');
            document.getElementById('AcompanharPedido').classList.toggle("show");
            break;
            
        case 'fechar-avaliacao':
            temp = document.getElementById('AvaliarPedido');
            break;
            
        case 'enviar-avaliacao':
            temp = document.getElementById('AvaliarPedido');
            enviarAvaliacao();
            break;

        case 'escolher-sabor':
            temp = document.getElementById('EscolherSabor');
            break;
        }
    
    temp.classList.toggle("show");
    esconderMain(temp);
}

function esconderMain(show) {
    
    const main = document.querySelector('.main')
    if(show.classList.contains('show')){
        main.style.display = 'none'
    } else {
        main.style.display = 'flex'
    }
}

function selecionarQualidade(element) {
    element.classList.toggle('selected')
}

// Preencher as estrelas na avaliação
function fillStar(starValue) {
    
    const stars = document.querySelectorAll('.star');
    
    // Remover a classe 'filled' de todas as estrelas
    stars.forEach(star => star.classList.remove('filled'));
    
    // Adicionar a classe 'filled' até a estrela clicada
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

// ajustarFooter()
// function ajustarFooter {
//     const windowHeight = window.innerHeight;
//     const footer = document.querySelector('footer')
//     footer.style.bottom = '-34px';
// }
