function adicionarAoCarrinho() {
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

// Mostra o escolher sabor da página combos
function escolherSabor() {
    const chooseFlavor = document.getElementById('choose-flavor');
    chooseFlavor.classList.toggle('show');
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