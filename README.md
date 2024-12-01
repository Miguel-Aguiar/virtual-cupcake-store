# Loja de Cupcakes Virtual

Acesse o site pelo link: https://virtual-cupcake-store.site/View/home.

Agora basta criar um registro simples(as informações não precisam ser verdadeiras) e você já tem acesso as funções do site.

Teste a vontade! Você pode adicionar e retirar itens do carrinho, fazer pedidos e criar seu próprio sabor personalizado, e até simular uma entrega!

---  

Caso queira subir o site na própria máquina basta seguir estes passos: 

1- Baixe o projeto no github clicando no botão verde e então Download ZIP

2- Baixar a ferramenta xampp.  
Link para o xampp: https://www.apachefriends.org/pt_br/index.html  

3- Agora execute o xampp. Quando for pedido para selecionar os componentes, deixe marcado apenas o MySQL e o phpMyAdmin além dos outros obrigatórios.  

4- Colocar a pasta do projeto 'virtual-cupcake-store-main' dentro da pasta htdocs do xampp.  

5- Com o xampp aberto clique em start para inicar o apache e o mysql.  
Obs.: Para o site funcionar tanto o apache quanto o MySQL devem estar iniciados.  

6- No navegador coloque o endereço http://localhost/phpmyadmin/ para abrir o banco de dados.  

7- Crie um novo banco de dados clicando em novo e coloque o nome 'cupcakesstore' ou insira esse código na aba sql 'CREATE DATABASE cupcakesstore;'  

8- Clique no banco de dados cupcakesstore para acessá-lo, abra a aba sql e então copie todo o código que está no arquivo cupcakesstore.sql, cole e execute.  
Obs.: O arquivo cupcakesstore.sql contém todas as tabelas a dados necessários para que a aplicação funcione.  

9- Agora abra o site no endereço: 'http://localhost/virtual-cupcake-store-main/View/home.php' e pronto.  

Dessa forma, você já vai ter acesso ao site, fique a vontade para testar e alterar qualquer funcionamento.
