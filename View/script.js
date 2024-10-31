function adicionarAoCarrinho() {
    // Mostra a notificação
    const notification = document.getElementById('notification');
    notification.classList.add('show');

    // Oculta a notificação após 3 segundos
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    cartSidebar.classList.toggle('show');
}