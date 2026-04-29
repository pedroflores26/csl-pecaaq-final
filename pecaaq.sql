-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/04/2026 às 02:26
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pecaaq`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `gerar_numero_pedido` (OUT `p_numero` VARCHAR(20))   BEGIN
  DECLARE v_seq INT;
  SELECT COUNT(*) + 1 INTO v_seq FROM `pedidos`;
  SET p_numero = CONCAT('PAQ-', YEAR(NOW()), '-', LPAD(v_seq, 5, '0'));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `acao` varchar(80) NOT NULL,
  `tabela` varchar(60) DEFAULT NULL,
  `registro_id` int(10) UNSIGNED DEFAULT NULL,
  `dados_antes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_antes`)),
  `dados_depois` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_depois`)),
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(300) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `usuario_id`, `acao`, `tabela`, `registro_id`, `dados_antes`, `dados_depois`, `ip`, `user_agent`, `criado_em`) VALUES
(1, 1, 'login', 'usuarios', 1, NULL, NULL, '127.0.0.1', NULL, '2026-04-28 00:03:12'),
(2, 2, 'login', 'usuarios', 2, NULL, NULL, '192.168.1.10', NULL, '2026-04-28 00:03:12'),
(3, 1, 'update', 'pedidos', 2, NULL, NULL, '127.0.0.1', NULL, '2026-04-28 00:03:12'),
(4, 1, 'create', 'cupons', 3, NULL, NULL, '127.0.0.1', NULL, '2026-04-28 00:03:12'),
(5, 3, 'login', 'usuarios', 3, NULL, NULL, '192.168.1.20', NULL, '2026-04-28 00:03:12'),
(6, 1, 'update', 'empresas', 1, NULL, NULL, '127.0.0.1', NULL, '2026-04-28 00:03:12'),
(7, 4, 'login', 'usuarios', 4, NULL, NULL, '200.100.50.10', NULL, '2026-04-28 00:03:12'),
(8, 5, 'login', 'usuarios', 5, NULL, NULL, '200.100.50.20', NULL, '2026-04-28 00:03:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED DEFAULT NULL,
  `nota` tinyint(4) NOT NULL CHECK (`nota` between 1 and 5),
  `titulo` varchar(120) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `verificada` tinyint(1) NOT NULL DEFAULT 0,
  `util_sim` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `util_nao` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `avaliacoes`
--
DELIMITER $$
CREATE TRIGGER `trg_atualiza_avaliacao_produto` AFTER INSERT ON `avaliacoes` FOR EACH ROW BEGIN
  UPDATE `produtos`
  SET avaliacao_media  = (SELECT AVG(nota)  FROM `avaliacoes` WHERE produto_id = NEW.produto_id),
      total_avaliacoes = (SELECT COUNT(*)   FROM `avaliacoes` WHERE produto_id = NEW.produto_id)
  WHERE id = NEW.produto_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `banners`
--

CREATE TABLE `banners` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(120) NOT NULL,
  `imagem_url` varchar(500) NOT NULL,
  `link` varchar(300) DEFAULT NULL,
  `posicao` enum('hero','topo','lateral','rodape') NOT NULL DEFAULT 'hero',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `ordem` smallint(6) NOT NULL DEFAULT 0,
  `inicio_em` date DEFAULT NULL,
  `fim_em` date DEFAULT NULL,
  `cliques` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinhos`
--

CREATE TABLE `carrinhos` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = sessão anônima',
  `sessao_token` varchar(100) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho_itens`
--

CREATE TABLE `carrinho_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `carrinho_id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `quantidade` smallint(6) NOT NULL DEFAULT 1,
  `preco_unit` decimal(10,2) NOT NULL COMMENT 'preço no momento da adição',
  `adicionado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `descricao` varchar(300) DEFAULT NULL,
  `icone` varchar(60) DEFAULT NULL COMMENT 'classe Font Awesome',
  `parent_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'categoria pai',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `ordem` smallint(6) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `slug`, `descricao`, `icone`, `parent_id`, `ativo`, `ordem`) VALUES
(1, 'Motor', 'motor', NULL, 'fa-cog', NULL, 1, 1),
(2, 'Suspensão', 'suspensao', NULL, 'fa-car-side', NULL, 1, 2),
(3, 'Freios', 'freios', NULL, 'fa-stop-circle', NULL, 1, 3),
(4, 'Elétrica', 'eletrica', NULL, 'fa-bolt', NULL, 1, 4),
(5, 'Transmissão', 'transmissao', NULL, 'fa-gears', NULL, 1, 5),
(6, 'Filtros', 'filtros', NULL, 'fa-filter', NULL, 1, 6),
(7, 'Ignição', 'ignicao', NULL, 'fa-fire', NULL, 1, 7),
(8, 'Arrefecimento', 'arrefecimento', NULL, 'fa-temperature-low', NULL, 1, 8),
(9, 'Carroceria', 'carroceria', NULL, 'fa-car', NULL, 1, 9),
(10, 'Acessórios', 'acessorios', NULL, 'fa-star', NULL, 1, 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `chave` varchar(80) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `descricao` varchar(300) DEFAULT NULL,
  `grupo` varchar(60) NOT NULL DEFAULT 'geral',
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `tipo` enum('percentual','fixo','frete_gratis') NOT NULL DEFAULT 'percentual',
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_minimo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `uso_maximo` int(11) DEFAULT NULL COMMENT 'NULL = ilimitado',
  `uso_atual` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `uso_por_usuario` tinyint(1) NOT NULL DEFAULT 1,
  `valido_de` date DEFAULT NULL,
  `valido_ate` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `razao_social` varchar(200) NOT NULL,
  `nome_fantasia` varchar(200) NOT NULL,
  `cnpj` varchar(18) NOT NULL,
  `inscricao_estadual` varchar(30) DEFAULT NULL,
  `email_comercial` varchar(180) NOT NULL,
  `telefone_comercial` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `website` varchar(300) DEFAULT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `categoria_principal` varchar(80) DEFAULT NULL,
  `endereco_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pendente','aprovada','suspensa','rejeitada') NOT NULL DEFAULT 'pendente',
  `verificada` tinyint(1) NOT NULL DEFAULT 0,
  `avaliacao_media` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_vendas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `empresas`
--

INSERT INTO `empresas` (`id`, `usuario_id`, `razao_social`, `nome_fantasia`, `cnpj`, `inscricao_estadual`, `email_comercial`, `telefone_comercial`, `whatsapp`, `website`, `logo_url`, `descricao`, `categoria_principal`, `endereco_id`, `status`, `verificada`, `avaliacao_media`, `total_vendas`, `criado_em`, `atualizado_em`) VALUES
(1, 2, 'AutoPeças São Paulo LTDA', 'AutoSP Peças', '12.345.678/0001-90', NULL, 'contato@autosp.com', '(11)3333-1111', '(11)99999-1111', NULL, NULL, NULL, NULL, 1, 'aprovada', 1, 4.70, 127, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(2, 3, 'Distribuidora Sul de Peças EIRELI', 'SulPeças RS', '98.765.432/0001-10', NULL, 'contato@sulpecas.com', '(51)3333-2222', '(51)98888-2222', NULL, NULL, NULL, NULL, 2, 'aprovada', 1, 4.30, 89, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(3, 12, 'pedro', 'Pedro', '18.927.668/0001-44', NULL, 'pp@gmail.com', '(12) 31221-3123', '(12) 31231-2312', NULL, NULL, NULL, 'Multimarcas', NULL, 'aprovada', 0, 0.00, 0, '2026-04-28 01:00:57', '2026-04-28 01:00:57'),
(4, 13, 'pneus', 'joaquim', '63.839.538/0001-04', NULL, 'joaquimbarbosa@gmail.com', '(22) 22222-2222', '(22) 22222-2222', NULL, NULL, NULL, 'Multimarcas', NULL, 'aprovada', 0, 0.00, 0, '2026-04-28 20:40:05', '2026-04-28 20:40:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int(10) UNSIGNED NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(200) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` char(2) NOT NULL,
  `pais` char(2) NOT NULL DEFAULT 'BR',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`id`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `pais`, `criado_em`, `atualizado_em`) VALUES
(1, '01310-100', 'Avenida Paulista', '1000', NULL, 'Bela Vista', 'São Paulo', 'SP', 'BR', '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(2, '90010-150', 'Rua dos Andradas', '850', NULL, 'Centro', 'Porto Alegre', 'RS', 'BR', '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(3, '30130-110', 'Avenida Afonso Pena', '2100', NULL, 'Centro', 'Belo Horizonte', 'MG', 'BR', '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(4, '80010-010', 'Rua XV de Novembro', '700', NULL, 'Centro', 'Curitiba', 'PR', 'BR', '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(5, '40020-020', 'Avenida Sete de Setembro', '500', NULL, 'Centro', 'Salvador', 'BA', 'BR', '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(6, '93260-420', 'Rua Ararangua', '180', 'casa', 'Tamandaré', 'Esteio', 'RS', 'BR', '2026-04-28 00:18:29', '2026-04-28 00:18:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `lista_desejos`
--

CREATE TABLE `lista_desejos` (
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `adicionado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `marcas`
--

CREATE TABLE `marcas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `pais` varchar(60) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `marcas`
--

INSERT INTO `marcas` (`id`, `nome`, `slug`, `logo_url`, `pais`, `ativo`) VALUES
(1, 'Monroe', 'monroe', NULL, 'EUA', 1),
(2, 'Moura', 'moura', NULL, 'Brasil', 1),
(3, 'Gates', 'gates', NULL, 'EUA', 1),
(4, 'NGK', 'ngk', NULL, 'Japão', 1),
(5, 'Bosch', 'bosch', NULL, 'Alemanha', 1),
(6, 'Cofap', 'cofap', NULL, 'Brasil', 1),
(7, 'Fras-le', 'fras-le', NULL, 'Brasil', 1),
(8, 'Mahle', 'mahle', NULL, 'Alemanha', 1),
(9, 'Shell', 'shell', NULL, 'Holanda', 1),
(10, 'SKF', 'skf', NULL, 'Suécia', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pedido','pagamento','produto','sistema','promocao','avaliacao') NOT NULL DEFAULT 'sistema',
  `titulo` varchar(120) NOT NULL,
  `mensagem` varchar(500) NOT NULL,
  `url` varchar(300) DEFAULT NULL,
  `icone` varchar(60) DEFAULT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `tipo`, `titulo`, `mensagem`, `url`, `icone`, `lida`, `criado_em`) VALUES
(1, 4, 'pedido', 'Pedido entregue!', 'Seu pedido PAQ-2026-00001 foi entregue com sucesso.', NULL, NULL, 1, '2026-04-28 00:03:12'),
(2, 4, 'pedido', 'Pedido aprovado', 'Pagamento do pedido PAQ-2026-00003 foi aprovado.', NULL, NULL, 0, '2026-04-28 00:03:12'),
(3, 5, 'pedido', 'Pedido enviado', 'Seu pedido PAQ-2026-00002 foi enviado. Rastreie pelo número BR987654321BR.', NULL, NULL, 0, '2026-04-28 00:03:12'),
(4, 5, 'promocao', 'Cupom exclusivo', 'Use PROMO15 e ganhe 15% de desconto em freios e suspensão até 30/04!', NULL, NULL, 0, '2026-04-28 00:03:12'),
(5, 6, 'pedido', 'Aguardando pagamento', 'Seu boleto do pedido PAQ-2026-00004 vence em 3 dias.', NULL, NULL, 0, '2026-04-28 00:03:12'),
(6, 1, 'sistema', 'Estoque crítico', '3 produtos estão abaixo do estoque mínimo.', NULL, NULL, 0, '2026-04-28 00:03:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(20) NOT NULL COMMENT 'ex: PAQ-2026-00001',
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `endereco_entrega_id` int(10) UNSIGNED DEFAULT NULL,
  `cupom_id` int(10) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `frete` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('aguardando_pagamento','pagamento_aprovado','em_separacao','enviado','entregue','cancelado','devolvido','reembolsado') NOT NULL DEFAULT 'aguardando_pagamento',
  `metodo_pagamento` enum('cartao_credito','cartao_debito','pix','boleto','transferencia') DEFAULT NULL,
  `parcelas` tinyint(4) NOT NULL DEFAULT 1,
  `gateway_id` varchar(100) DEFAULT NULL,
  `nota_fiscal` varchar(60) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `rastreamento` varchar(50) DEFAULT NULL,
  `transportadora` varchar(80) DEFAULT NULL,
  `previsao_entrega` date DEFAULT NULL,
  `entregue_em` datetime DEFAULT NULL,
  `cancelado_em` datetime DEFAULT NULL,
  `motivo_cancelamento` varchar(300) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `numero`, `usuario_id`, `endereco_entrega_id`, `cupom_id`, `subtotal`, `desconto`, `frete`, `total`, `status`, `metodo_pagamento`, `parcelas`, `gateway_id`, `nota_fiscal`, `observacao`, `rastreamento`, `transportadora`, `previsao_entrega`, `entregue_em`, `cancelado_em`, `motivo_cancelamento`, `criado_em`, `atualizado_em`) VALUES
(1, 'PAQ-2026-00001', 4, 1, NULL, 259.80, 0.00, 0.00, 259.80, 'entregue', 'pix', 1, 'PIX001', NULL, NULL, 'BR123456789BR', 'Correios', '2026-04-10', '2026-04-09 14:30:00', NULL, NULL, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(2, 'PAQ-2026-00002', 5, 2, NULL, 469.90, 0.00, 25.00, 494.90, 'enviado', 'cartao_credito', 3, 'CARD002', NULL, NULL, 'BR987654321BR', 'JadLog', '2026-04-30', NULL, NULL, NULL, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(3, 'PAQ-2026-00003', 4, 1, NULL, 149.80, 20.00, 15.00, 144.80, 'pagamento_aprovado', 'cartao_debito', 1, 'CARD003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(4, 'PAQ-2026-00004', 6, 3, NULL, 89.90, 0.00, 12.00, 101.90, 'aguardando_pagamento', 'boleto', 1, 'BOL004', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(5, 'PAQ-2026-00005', 5, 2, NULL, 549.80, 0.00, 0.00, 549.80, 'em_separacao', 'pix', 1, 'PIX005', NULL, NULL, NULL, 'Correios', '2026-05-05', NULL, NULL, NULL, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(6, 'PAQ-2026-00006', 4, 1, NULL, 79.90, 0.00, 8.00, 87.90, 'cancelado', 'cartao_credito', 1, 'CARD006', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28 00:03:12', '2026-04-28 00:03:12');

--
-- Acionadores `pedidos`
--
DELIMITER $$
CREATE TRIGGER `trg_incrementa_vendas` AFTER UPDATE ON `pedidos` FOR EACH ROW BEGIN
  IF NEW.status = 'entregue' AND OLD.status != 'entregue' THEN
    UPDATE `produtos` pr
    JOIN `pedido_itens` pi ON pi.produto_id = pr.id AND pi.pedido_id = NEW.id
    SET pr.total_vendas = pr.total_vendas + pi.quantidade;

    UPDATE `empresas` e
    JOIN `pedido_itens` pi ON pi.empresa_id = e.id AND pi.pedido_id = NEW.id
    SET e.total_vendas = e.total_vendas + 1;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_historico`
--

CREATE TABLE `pedido_historico` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(60) NOT NULL,
  `descricao` varchar(400) DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'quem fez a mudança',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_historico`
--

INSERT INTO `pedido_historico` (`id`, `pedido_id`, `status`, `descricao`, `usuario_id`, `criado_em`) VALUES
(1, 1, 'aguardando_pagamento', 'Pedido criado.', NULL, '2026-04-28 00:03:12'),
(2, 1, 'pagamento_aprovado', 'PIX confirmado automaticamente.', NULL, '2026-04-28 00:03:12'),
(3, 1, 'em_separacao', 'Pedido em separação no estoque.', NULL, '2026-04-28 00:03:12'),
(4, 1, 'enviado', 'Postado nos Correios. Rastreio: BR123456789BR', NULL, '2026-04-28 00:03:12'),
(5, 1, 'entregue', 'Entrega confirmada pelo cliente.', NULL, '2026-04-28 00:03:12'),
(6, 2, 'aguardando_pagamento', 'Pedido criado.', NULL, '2026-04-28 00:03:12'),
(7, 2, 'pagamento_aprovado', 'Cartão aprovado.', NULL, '2026-04-28 00:03:12'),
(8, 2, 'em_separacao', 'Em separação.', NULL, '2026-04-28 00:03:12'),
(9, 2, 'enviado', 'Enviado via JadLog.', NULL, '2026-04-28 00:03:12'),
(10, 3, 'aguardando_pagamento', 'Pedido criado.', NULL, '2026-04-28 00:03:12'),
(11, 3, 'pagamento_aprovado', 'Cartão débito aprovado.', NULL, '2026-04-28 00:03:12'),
(12, 4, 'aguardando_pagamento', 'Aguardando pagamento do boleto.', NULL, '2026-04-28 00:03:12'),
(13, 5, 'aguardando_pagamento', 'Pedido criado.', NULL, '2026-04-28 00:03:12'),
(14, 5, 'pagamento_aprovado', 'PIX confirmado.', NULL, '2026-04-28 00:03:12'),
(15, 5, 'em_separacao', 'Separando itens.', NULL, '2026-04-28 00:03:12'),
(16, 6, 'aguardando_pagamento', 'Pedido criado.', NULL, '2026-04-28 00:03:12'),
(17, 6, 'cancelado', 'Cancelado a pedido do cliente.', NULL, '2026-04-28 00:03:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `nome_produto` varchar(200) NOT NULL COMMENT 'snapshot do nome',
  `sku` varchar(60) NOT NULL,
  `quantidade` smallint(6) NOT NULL DEFAULT 1,
  `preco_unit` decimal(10,2) NOT NULL,
  `desconto_unit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status_item` enum('ativo','cancelado','devolvido') NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL,
  `marca_id` int(10) UNSIGNED DEFAULT NULL,
  `nome` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `descricao` text DEFAULT NULL,
  `descricao_curta` varchar(500) DEFAULT NULL,
  `sku` varchar(60) NOT NULL COMMENT 'código interno',
  `codigo_oem` varchar(80) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_custo` decimal(10,2) DEFAULT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `promocao_inicio` date DEFAULT NULL,
  `promocao_fim` date DEFAULT NULL,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `estoque_minimo` int(11) NOT NULL DEFAULT 5,
  `peso_kg` decimal(6,3) DEFAULT NULL,
  `disponibilidade` enum('em_estoque','pronta_entrega','encomenda','esgotado') NOT NULL DEFAULT 'em_estoque',
  `imagem_principal` varchar(500) DEFAULT NULL,
  `status` enum('ativo','inativo','rascunho','pendente') NOT NULL DEFAULT 'pendente',
  `destaque` tinyint(1) NOT NULL DEFAULT 0,
  `avaliacao_media` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_avaliacoes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_vendas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `visualizacoes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `empresa_id`, `categoria_id`, `marca_id`, `nome`, `slug`, `descricao`, `descricao_curta`, `sku`, `codigo_oem`, `preco`, `preco_custo`, `preco_promocional`, `promocao_inicio`, `promocao_fim`, `estoque`, `estoque_minimo`, `peso_kg`, `disponibilidade`, `imagem_principal`, `status`, `destaque`, `avaliacao_media`, `total_avaliacoes`, `total_vendas`, `visualizacoes`, `criado_em`, `atualizado_em`) VALUES
(1, 3, 3, 5, 'sandes girafa', 'sandes-girafa-69f119ea99927', 'uma bela girafa', NULL, '1', NULL, 200.00, NULL, NULL, NULL, NULL, 2, 5, NULL, 'em_estoque', 'prod_69f119ea99adc.png', 'ativo', 0, 0.00, 0, 0, 0, '2026-04-28 20:34:50', '2026-04-28 20:34:50'),
(3, 4, 8, 6, 'dasd', 'dasd-69f11b941cbea', 'dasda', NULL, 'sad', NULL, 2.00, NULL, NULL, NULL, NULL, 2, 5, NULL, 'em_estoque', 'prod_69f11b941cd2d.png', 'ativo', 0, 0.00, 0, 0, 0, '2026-04-28 20:41:56', '2026-04-28 20:41:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_compatibilidade`
--

CREATE TABLE `produto_compatibilidade` (
  `produto_id` int(10) UNSIGNED NOT NULL,
  `veiculo_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_imagens`
--

CREATE TABLE `produto_imagens` (
  `id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `url` varchar(500) NOT NULL,
  `alt` varchar(200) DEFAULT NULL,
  `ordem` smallint(6) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `repasses`
--

CREATE TABLE `repasses` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `valor_bruto` decimal(10,2) NOT NULL,
  `taxa_plat` decimal(5,2) NOT NULL DEFAULT 5.00 COMMENT '% taxa PeçaAQ',
  `valor_taxa` decimal(10,2) NOT NULL,
  `valor_liquido` decimal(10,2) NOT NULL,
  `status` enum('pendente','processando','pago','estornado') NOT NULL DEFAULT 'pendente',
  `data_pagamento` date DEFAULT NULL,
  `comprovante` varchar(500) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `repasses`
--

INSERT INTO `repasses` (`id`, `empresa_id`, `pedido_id`, `valor_bruto`, `taxa_plat`, `valor_taxa`, `valor_liquido`, `status`, `data_pagamento`, `comprovante`, `criado_em`) VALUES
(1, 2, 1, 259.80, 5.00, 12.99, 246.81, 'pago', '2026-04-16', NULL, '2026-04-28 00:03:12'),
(2, 1, 2, 469.90, 5.00, 23.50, 446.40, 'pendente', NULL, NULL, '2026-04-28 00:03:12'),
(3, 1, 3, 149.80, 5.00, 7.49, 142.31, 'pendente', NULL, NULL, '2026-04-28 00:03:12'),
(4, 2, 4, 89.90, 5.00, 4.50, 85.40, 'pendente', NULL, NULL, '2026-04-28 00:03:12'),
(5, 1, 5, 549.80, 5.00, 27.49, 522.31, 'processando', NULL, NULL, '2026-04-28 00:03:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tickets`
--

CREATE TABLE `tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(20) NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED DEFAULT NULL,
  `assunto` varchar(200) NOT NULL,
  `categoria` enum('entrega','pagamento','produto','devolucao','outros') NOT NULL DEFAULT 'outros',
  `prioridade` enum('baixa','media','alta','urgente') NOT NULL DEFAULT 'media',
  `status` enum('aberto','em_atendimento','aguardando_cliente','resolvido','fechado') NOT NULL DEFAULT 'aberto',
  `atribuido_a` int(10) UNSIGNED DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolvido_em` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tickets`
--

INSERT INTO `tickets` (`id`, `numero`, `usuario_id`, `pedido_id`, `assunto`, `categoria`, `prioridade`, `status`, `atribuido_a`, `criado_em`, `atualizado_em`, `resolvido_em`) VALUES
(1, 'TKT-2026-00001', 5, 2, 'Produto chegou com embalagem danificada', 'produto', 'alta', 'fechado', 1, '2026-04-28 00:03:12', '2026-04-28 20:44:59', '2026-04-28 17:44:59'),
(2, 'TKT-2026-00002', 4, 3, 'Quando meu pedido vai chegar?', 'entrega', 'media', 'fechado', 1, '2026-04-28 00:03:12', '2026-04-28 20:45:00', '2026-04-28 17:45:00'),
(3, 'TKT-2026-00003', 6, 4, 'Não consigo pagar o boleto', 'pagamento', 'urgente', 'fechado', NULL, '2026-04-28 00:03:12', '2026-04-28 20:44:57', '2026-04-28 17:44:57'),
(4, 'TKT-2026-00004', 4, 1, 'Avaliação não apareceu no site', 'produto', 'baixa', 'fechado', 1, '2026-04-28 00:03:12', '2026-04-28 00:03:12', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ticket_mensagens`
--

CREATE TABLE `ticket_mensagens` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `mensagem` text NOT NULL,
  `anexo_url` varchar(500) DEFAULT NULL,
  `interno` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'nota interna só para admin',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `sobrenome` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `senha_hash` varchar(255) NOT NULL COMMENT 'bcrypt hash',
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `tipo` enum('cliente','empresa','admin') NOT NULL DEFAULT 'cliente',
  `status` enum('ativo','inativo','suspenso','pendente') NOT NULL DEFAULT 'pendente',
  `email_verificado` tinyint(1) NOT NULL DEFAULT 0,
  `token_verificacao` varchar(100) DEFAULT NULL,
  `token_recuperacao` varchar(100) DEFAULT NULL,
  `token_expira_em` datetime DEFAULT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `endereco_id` int(10) UNSIGNED DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `sobrenome`, `email`, `senha_hash`, `cpf`, `telefone`, `data_nascimento`, `avatar_url`, `tipo`, `status`, `email_verificado`, `token_verificacao`, `token_recuperacao`, `token_expira_em`, `ultimo_login`, `endereco_id`, `criado_em`, `atualizado_em`) VALUES
(1, 'Admin', 'PeçaAQ', 'admin@pecaaq.com', '$2y$10$5IAiLClOfHgnHKSca2.qO.kucXGpaSbynI8Ha43k9eGeRpM4r6SYu', NULL, NULL, NULL, NULL, 'admin', 'ativo', 1, NULL, NULL, NULL, '2026-04-26 10:00:00', 1, '2026-04-28 00:03:12', '2026-04-28 20:25:45'),
(2, 'Fornecedor', 'AutoPeças SP', 'fornecedor1@autosp.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12.345.678/000', '(11)99999-1111', NULL, NULL, 'empresa', 'ativo', 1, NULL, NULL, NULL, '2026-04-25 14:00:00', 1, '2026-04-28 00:03:12', '2026-04-28 00:07:13'),
(3, 'Distribuidora', 'Sul Peças RS', 'fornecedor2@sulpecas.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '98.765.432/000', '(51)98888-2222', NULL, NULL, 'empresa', 'ativo', 1, NULL, NULL, NULL, '2026-04-24 09:00:00', 2, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(4, 'Carlos', 'Oliveira', 'carlos@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '111.222.333-44', '(11)91234-5678', NULL, NULL, 'cliente', 'ativo', 1, NULL, NULL, NULL, '2026-04-26 08:30:00', 1, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(5, 'Maria', 'Souza', 'maria@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '222.333.444-55', '(51)92345-6789', NULL, NULL, 'cliente', 'ativo', 1, NULL, NULL, NULL, '2026-04-25 17:45:00', 2, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(6, 'João', 'Ferreira', 'joao@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '333.444.555-66', '(31)93456-7890', NULL, NULL, 'cliente', 'pendente', 0, NULL, NULL, NULL, NULL, 3, '2026-04-28 00:03:12', '2026-04-28 00:07:26'),
(7, 'Ana', 'Lima', 'ana@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '444.555.666-77', '(41)94567-8901', NULL, NULL, 'cliente', 'pendente', 1, NULL, NULL, NULL, NULL, 4, '2026-04-28 00:03:12', '2026-04-28 00:07:32'),
(8, 'Pedro', 'Costa', 'pedro@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555.666.777-88', '(71)95678-9012', NULL, NULL, 'cliente', 'suspenso', 1, NULL, NULL, NULL, '2026-04-10 12:00:00', 5, '2026-04-28 00:03:12', '2026-04-28 00:03:12'),
(9, 'PEDRO', 'flores', 'ppedrrao@gmail.com', '$2y$12$QEhK703VBCw8EZCvnVR0WOSn50cMxIiIcvsYvzX7ZPrClhHw950ki', '066.432.340-50', '(51) 99411-7445', '2007-07-26', NULL, 'cliente', 'ativo', 0, '54', NULL, NULL, NULL, 6, '2026-04-28 00:18:29', '2026-04-28 00:18:29'),
(10, 'dasd', '', 'pedrofloresbasso@gmail.com', '$2y$10$t70hO7FPrh4/2nKWlVPHt.gRoLzlAPXpVK4VS.jEENt/jy6mKFJPi', NULL, NULL, NULL, NULL, 'cliente', 'pendente', 0, NULL, NULL, NULL, NULL, NULL, '2026-04-28 00:29:50', '2026-04-28 00:29:50'),
(12, 'Pedro', '', 'pp@gmail.com', '$2y$10$Yxru1O/xHIxdTrwB2vfzf.b7RRd9rdmKkF5xwj2txIgWbhR1A9HLe', NULL, NULL, NULL, NULL, 'empresa', 'pendente', 0, NULL, NULL, NULL, NULL, NULL, '2026-04-28 01:00:57', '2026-04-28 01:00:57'),
(13, 'joaquim', '', 'joaquimbarbosa@gmail.com', '$2y$10$UPpaXJS/g0iGfcBZjqTDz.3uLM8xvK/DaBI.C4.XK4zV1rYWttS5u', NULL, NULL, NULL, NULL, 'empresa', 'pendente', 0, NULL, NULL, NULL, NULL, NULL, '2026-04-28 20:40:05', '2026-04-28 20:40:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `id` int(10) UNSIGNED NOT NULL,
  `marca` varchar(60) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ano_inicio` year(4) NOT NULL,
  `ano_fim` year(4) DEFAULT NULL,
  `motor` varchar(40) DEFAULT NULL,
  `combustivel` enum('gasolina','flex','diesel','eletrico','hibrido') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`id`, `marca`, `modelo`, `ano_inicio`, `ano_fim`, `motor`, `combustivel`) VALUES
(1, 'Volkswagen', 'Gol', '2010', '2023', '1.0', 'flex'),
(2, 'Volkswagen', 'Polo', '2018', NULL, '1.0', 'flex'),
(3, 'Fiat', 'Uno', '2010', '2023', '1.0', 'flex'),
(4, 'Fiat', 'Palio', '2008', '2018', '1.0', 'flex'),
(5, 'Fiat', 'Strada', '2020', NULL, '1.3', 'flex'),
(6, 'Chevrolet', 'Onix', '2012', NULL, '1.0', 'flex'),
(7, 'Chevrolet', 'Celta', '2002', '2016', '1.0', 'flex'),
(8, 'Toyota', 'Corolla', '2015', NULL, '2.0', 'flex'),
(9, 'Honda', 'Civic', '2016', NULL, '1.5', 'gasolina'),
(10, 'Hyundai', 'HB20', '2012', NULL, '1.0', 'flex');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_estoque_critico`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_estoque_critico` (
`id` int(10) unsigned
,`nome` varchar(200)
,`sku` varchar(60)
,`estoque` int(11)
,`estoque_minimo` int(11)
,`empresa` varchar(200)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_pedidos_cliente`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_pedidos_cliente` (
`id` int(10) unsigned
,`numero` varchar(20)
,`status` enum('aguardando_pagamento','pagamento_aprovado','em_separacao','enviado','entregue','cancelado','devolvido','reembolsado')
,`total` decimal(10,2)
,`criado_em` timestamp
,`nome` varchar(120)
,`email` varchar(180)
,`qtd_itens` bigint(21)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_vendas_empresa`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_vendas_empresa` (
`empresa_id` int(10) unsigned
,`nome_fantasia` varchar(200)
,`total_pedidos` bigint(21)
,`receita_bruta` decimal(32,2)
,`receita_liquida` decimal(32,2)
,`avaliacao` decimal(7,6)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_estoque_critico`
--
DROP TABLE IF EXISTS `vw_estoque_critico`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_estoque_critico`  AS SELECT `pr`.`id` AS `id`, `pr`.`nome` AS `nome`, `pr`.`sku` AS `sku`, `pr`.`estoque` AS `estoque`, `pr`.`estoque_minimo` AS `estoque_minimo`, `e`.`nome_fantasia` AS `empresa` FROM (`produtos` `pr` join `empresas` `e` on(`e`.`id` = `pr`.`empresa_id`)) WHERE `pr`.`estoque` <= `pr`.`estoque_minimo` AND `pr`.`status` = 'ativo' ORDER BY `pr`.`estoque` ASC ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_pedidos_cliente`
--
DROP TABLE IF EXISTS `vw_pedidos_cliente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pedidos_cliente`  AS SELECT `p`.`id` AS `id`, `p`.`numero` AS `numero`, `p`.`status` AS `status`, `p`.`total` AS `total`, `p`.`criado_em` AS `criado_em`, `u`.`nome` AS `nome`, `u`.`email` AS `email`, count(`pi`.`id`) AS `qtd_itens` FROM ((`pedidos` `p` join `usuarios` `u` on(`u`.`id` = `p`.`usuario_id`)) join `pedido_itens` `pi` on(`pi`.`pedido_id` = `p`.`id`)) GROUP BY `p`.`id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_vendas_empresa`
--
DROP TABLE IF EXISTS `vw_vendas_empresa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_vendas_empresa`  AS SELECT `e`.`id` AS `empresa_id`, `e`.`nome_fantasia` AS `nome_fantasia`, count(distinct `p`.`id`) AS `total_pedidos`, sum(`pi`.`total`) AS `receita_bruta`, sum(`r`.`valor_liquido`) AS `receita_liquida`, avg(`e`.`avaliacao_media`) AS `avaliacao` FROM (((`empresas` `e` left join `pedido_itens` `pi` on(`pi`.`empresa_id` = `e`.`id`)) left join `pedidos` `p` on(`p`.`id` = `pi`.`pedido_id` and `p`.`status` not in ('cancelado','devolvido'))) left join `repasses` `r` on(`r`.`empresa_id` = `e`.`id` and `r`.`status` = 'pago')) GROUP BY `e`.`id` ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_usuario` (`usuario_id`),
  ADD KEY `idx_log_acao` (`acao`),
  ADD KEY `idx_log_criado` (`criado_em`);

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_avaliacao` (`produto_id`,`usuario_id`),
  ADD KEY `fk_aval_usuario` (`usuario_id`);

--
-- Índices de tabela `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carr_usuario` (`usuario_id`);

--
-- Índices de tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ci_carrinho` (`carrinho_id`),
  ADD KEY `fk_ci_produto` (`produto_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_cat_parent` (`parent_id`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`chave`);

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`),
  ADD KEY `idx_empresa_cnpj` (`cnpj`),
  ADD KEY `idx_empresa_status` (`status`),
  ADD KEY `fk_empresa_endereco` (`endereco_id`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lista_desejos`
--
ALTER TABLE `lista_desejos`
  ADD PRIMARY KEY (`usuario_id`,`produto_id`),
  ADD KEY `fk_ld_produto` (`produto_id`);

--
-- Índices de tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_usuario` (`usuario_id`,`lida`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `idx_pedido_usuario` (`usuario_id`),
  ADD KEY `idx_pedido_status` (`status`),
  ADD KEY `idx_pedido_criado` (`criado_em`),
  ADD KEY `fk_pedido_endereco` (`endereco_entrega_id`),
  ADD KEY `fk_pedido_cupom` (`cupom_id`);

--
-- Índices de tabela `pedido_historico`
--
ALTER TABLE `pedido_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ph_pedido` (`pedido_id`),
  ADD KEY `fk_ph_usuario` (`usuario_id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pi_pedido` (`pedido_id`),
  ADD KEY `fk_pi_produto` (`produto_id`),
  ADD KEY `fk_pi_empresa` (`empresa_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_produto_marca` (`marca_id`),
  ADD KEY `idx_produto_empresa` (`empresa_id`),
  ADD KEY `idx_produto_categoria` (`categoria_id`),
  ADD KEY `idx_produto_status` (`status`),
  ADD KEY `idx_produto_preco` (`preco`);
ALTER TABLE `produtos` ADD FULLTEXT KEY `idx_produto_busca` (`nome`,`descricao`,`sku`);

--
-- Índices de tabela `produto_compatibilidade`
--
ALTER TABLE `produto_compatibilidade`
  ADD PRIMARY KEY (`produto_id`,`veiculo_id`),
  ADD KEY `fk_compat_veiculo` (`veiculo_id`);

--
-- Índices de tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pimg_produto` (`produto_id`);

--
-- Índices de tabela `repasses`
--
ALTER TABLE `repasses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rep_empresa` (`empresa_id`),
  ADD KEY `fk_rep_pedido` (`pedido_id`);

--
-- Índices de tabela `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `fk_tick_usuario` (`usuario_id`),
  ADD KEY `fk_tick_pedido` (`pedido_id`),
  ADD KEY `fk_tick_atribuido` (`atribuido_a`);

--
-- Índices de tabela `ticket_mensagens`
--
ALTER TABLE `ticket_mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tm_ticket` (`ticket_id`),
  ADD KEY `fk_tm_usuario` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_usuario_email` (`email`),
  ADD KEY `idx_usuario_tipo` (`tipo`),
  ADD KEY `idx_usuario_status` (`status`),
  ADD KEY `fk_usuario_endereco` (`endereco_id`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `pedido_historico`
--
ALTER TABLE `pedido_historico`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `repasses`
--
ALTER TABLE `repasses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `ticket_mensagens`
--
ALTER TABLE `ticket_mensagens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `fk_aval_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aval_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD CONSTRAINT `fk_carr_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD CONSTRAINT `fk_ci_carrinho` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinhos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ci_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `fk_empresa_endereco` FOREIGN KEY (`endereco_id`) REFERENCES `enderecos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_empresa_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `lista_desejos`
--
ALTER TABLE `lista_desejos`
  ADD CONSTRAINT `fk_ld_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ld_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedido_endereco` FOREIGN KEY (`endereco_entrega_id`) REFERENCES `enderecos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `pedido_historico`
--
ALTER TABLE `pedido_historico`
  ADD CONSTRAINT `fk_ph_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ph_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `fk_pi_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `fk_pi_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pi_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `fk_produto_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_produto_marca` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `produto_compatibilidade`
--
ALTER TABLE `produto_compatibilidade`
  ADD CONSTRAINT `fk_compat_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compat_veiculo` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD CONSTRAINT `fk_pimg_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `repasses`
--
ALTER TABLE `repasses`
  ADD CONSTRAINT `fk_rep_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `fk_rep_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`);

--
-- Restrições para tabelas `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tick_atribuido` FOREIGN KEY (`atribuido_a`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tick_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tick_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `ticket_mensagens`
--
ALTER TABLE `ticket_mensagens`
  ADD CONSTRAINT `fk_tm_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tm_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_endereco` FOREIGN KEY (`endereco_id`) REFERENCES `enderecos` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
