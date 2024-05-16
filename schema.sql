-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.7.0.6850
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para logistic_module
CREATE DATABASE IF NOT EXISTS `logistic_module` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `logistic_module`;

-- Copiando estrutura para tabela logistic_module.categoria_produtos
CREATE TABLE IF NOT EXISTS `categoria_produtos` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(255) DEFAULT NULL,
  `situacao` int(11) DEFAULT NULL,
  `codigo_categoria_produto` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_categoria`),
  KEY `codigo_categoria_produto` (`codigo_categoria_produto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela logistic_module.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nome_cliente` varchar(255) DEFAULT NULL,
  `email_cliente` varchar(255) DEFAULT NULL,
  `senha_cliente` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela logistic_module.dados_veiculo
CREATE TABLE IF NOT EXISTS `dados_veiculo` (
  `id_dados_veiculo` int(11) NOT NULL,
  `marca_veiculo` varchar(255) DEFAULT NULL,
  `nome_veiculo` varchar(255) DEFAULT NULL,
  `ano_veiculo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_dados_veiculo`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela logistic_module.estoque
CREATE TABLE IF NOT EXISTS `estoque` (
  `id_produto` int(11) NOT NULL AUTO_INCREMENT,
  `nome_produto` varchar(255) DEFAULT NULL,
  `descricao_produto` varchar(255) DEFAULT NULL,
  `valor_produto` varchar(50) DEFAULT NULL,
  `quantidade_produto` int(11) DEFAULT NULL,
  `codigo_categoria_produto` int(11) DEFAULT NULL,
  `situacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_produto`),
  KEY `categoria_produto` (`codigo_categoria_produto`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela logistic_module.lista_clientes
CREATE TABLE IF NOT EXISTS `lista_clientes` (
  `id_cliente_lista` int(11) NOT NULL AUTO_INCREMENT,
  `nome_cliente_lista` varchar(255) DEFAULT NULL,
  `email_cliente_lista` varchar(255) DEFAULT NULL,
  `telefone_cliente_lista` varchar(50) DEFAULT NULL,
  `endereco_cliente_lista` varchar(255) DEFAULT NULL,
  `situacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_cliente_lista`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela logistic_module.pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id_pedido` int(11) DEFAULT NULL,
  `nome_cliente_pedido` varchar(255) DEFAULT NULL,
  `codigo_categoria_pedido` int(11) DEFAULT NULL,
  `valor_pedido` varchar(255) DEFAULT NULL,
  KEY `codigo_categoria_pedido` (`codigo_categoria_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
