function adicionarAoCarrinho() {
    // Mostra a notificação
    const notification = document.getElementById('notification');
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