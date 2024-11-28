-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/11/2024 às 15:46
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cupcakesstore`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `idCarrinho` int(11) NOT NULL,
  `idCliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho_combo`
--

CREATE TABLE `carrinho_combo` (
  `idCarrinho` int(11) NOT NULL,
  `idCombo` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 1 CHECK (`quantidade` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho_cupcake`
--

CREATE TABLE `carrinho_cupcake` (
  `idCarrinho` int(11) NOT NULL,
  `idCupcake` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `idCliente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `numeroContato` varchar(20) NOT NULL,
  `restricoes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabela de Clientes';

-- --------------------------------------------------------

--
-- Estrutura para tabela `combo`
--

CREATE TABLE `combo` (
  `idCombo` int(11) NOT NULL,
  `tamanho` enum('pequeno','médio','grande') NOT NULL,
  `preco` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `combo`
--

INSERT INTO `combo` (`idCombo`, `tamanho`, `preco`) VALUES
(1, 'pequeno', 37.05),
(2, 'médio', 60.75),
(3, 'grande', 119.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `combo_cupcake`
--

CREATE TABLE `combo_cupcake` (
  `idCombo` int(11) NOT NULL,
  `idCupcake` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `combo_cupcake`
--

INSERT INTO `combo_cupcake` (`idCombo`, `idCupcake`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 4),
(2, 5),
(2, 10),
(3, 3),
(3, 8),
(3, 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupcake`
--

CREATE TABLE `cupcake` (
  `idCupcake` int(11) NOT NULL,
  `sabor` varchar(100) NOT NULL,
  `descricao` varchar(300) NOT NULL,
  `preco` decimal(7,2) NOT NULL,
  `restricoes` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabela de Cupcakes';

--
-- Despejando dados para a tabela `cupcake`
--

INSERT INTO `cupcake` (`idCupcake`, `sabor`, `descricao`, `preco`, `restricoes`) VALUES
(1, 'Chocolate', 'Cupcake de chocolate com cobertura de ganache.', 4.50, 'Lactose'),
(2, 'Baunilha', 'Cupcake de baunilha.', 3.50, ''),
(3, 'Avelã', 'Cupcake de avelã com nozes.', 5.00, 'Nozes, Lactose'),
(4, 'Lemonade', 'Cupcake de limão com cobertura de merengue.', 4.00, ''),
(5, 'Cenoura', 'Cupcake de cenoura com cobertura de chocolate.', 4.75, ''),
(8, 'Maracujá', 'Cupcake de maracujá com cobertura de mousse de maracujá.', 4.00, ''),
(9, 'Pistache', 'Cupcake de pistache com cobertura de creme de pistache.', 5.00, ''),
(10, 'Morango', 'Cupcake de morango com cobertura de chantilly.', 4.75, '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `idPedido` int(11) NOT NULL,
  `idCliente` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_combo`
--

CREATE TABLE `pedido_combo` (
  `idPedido` int(11) NOT NULL,
  `idCombo` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_cupcake`
--

CREATE TABLE `pedido_cupcake` (
  `idPedido` int(11) NOT NULL,
  `idCupcake` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`idCarrinho`),
  ADD KEY `idCliente` (`idCliente`);

--
-- Índices de tabela `carrinho_combo`
--
ALTER TABLE `carrinho_combo`
  ADD PRIMARY KEY (`idCarrinho`,`idCombo`),
  ADD KEY `idCombo` (`idCombo`);

--
-- Índices de tabela `carrinho_cupcake`
--
ALTER TABLE `carrinho_cupcake`
  ADD PRIMARY KEY (`idCarrinho`,`idCupcake`),
  ADD KEY `idCupcake` (`idCupcake`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idCliente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `combo`
--
ALTER TABLE `combo`
  ADD PRIMARY KEY (`idCombo`);

--
-- Índices de tabela `combo_cupcake`
--
ALTER TABLE `combo_cupcake`
  ADD PRIMARY KEY (`idCombo`,`idCupcake`),
  ADD KEY `idCupcake` (`idCupcake`);

--
-- Índices de tabela `cupcake`
--
ALTER TABLE `cupcake`
  ADD PRIMARY KEY (`idCupcake`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`idPedido`),
  ADD KEY `idCliente` (`idCliente`);

--
-- Índices de tabela `pedido_combo`
--
ALTER TABLE `pedido_combo`
  ADD PRIMARY KEY (`idPedido`,`idCombo`),
  ADD KEY `idCombo` (`idCombo`);

--
-- Índices de tabela `pedido_cupcake`
--
ALTER TABLE `pedido_cupcake`
  ADD PRIMARY KEY (`idPedido`,`idCupcake`),
  ADD KEY `idCupcake` (`idCupcake`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `idCarrinho` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idCliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `combo`
--
ALTER TABLE `combo`
  MODIFY `idCombo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `cupcake`
--
ALTER TABLE `cupcake`
  MODIFY `idCupcake` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `cliente` (`idCliente`) ON DELETE CASCADE;

--
-- Restrições para tabelas `carrinho_combo`
--
ALTER TABLE `carrinho_combo`
  ADD CONSTRAINT `carrinho_combo_ibfk_1` FOREIGN KEY (`idCarrinho`) REFERENCES `carrinho` (`idCarrinho`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrinho_combo_ibfk_2` FOREIGN KEY (`idCombo`) REFERENCES `combo` (`idCombo`) ON DELETE CASCADE;

--
-- Restrições para tabelas `carrinho_cupcake`
--
ALTER TABLE `carrinho_cupcake`
  ADD CONSTRAINT `carrinho_cupcake_ibfk_1` FOREIGN KEY (`idCarrinho`) REFERENCES `carrinho` (`idCarrinho`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrinho_cupcake_ibfk_2` FOREIGN KEY (`idCupcake`) REFERENCES `cupcake` (`idCupcake`) ON DELETE CASCADE;

--
-- Restrições para tabelas `combo_cupcake`
--
ALTER TABLE `combo_cupcake`
  ADD CONSTRAINT `combo_cupcake_ibfk_1` FOREIGN KEY (`idCombo`) REFERENCES `combo` (`idCombo`),
  ADD CONSTRAINT `combo_cupcake_ibfk_2` FOREIGN KEY (`idCupcake`) REFERENCES `cupcake` (`idCupcake`);

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `cliente` (`idCliente`);

--
-- Restrições para tabelas `pedido_combo`
--
ALTER TABLE `pedido_combo`
  ADD CONSTRAINT `pedido_combo_ibfk_1` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pedido_combo_ibfk_2` FOREIGN KEY (`idCombo`) REFERENCES `combo` (`idCombo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido_cupcake`
--
ALTER TABLE `pedido_cupcake`
  ADD CONSTRAINT `pedido_cupcake_ibfk_1` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`idPedido`),
  ADD CONSTRAINT `pedido_cupcake_ibfk_2` FOREIGN KEY (`idCupcake`) REFERENCES `cupcake` (`idCupcake`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
