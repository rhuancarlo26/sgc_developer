CREATE TABLE `supressao_relatorio`
(
    `id`              int(11)  NOT NULL AUTO_INCREMENT,
    `fk_servico`      int(11)  NOT NULL,
    `fk_resultado`    int(11)  NOT NULL,
    `nome_relatorio`  tinytext NOT NULL,
    `conclusao`       tinytext NOT NULL,
    `sobre_relatorio` tinytext NOT NULL,
    `fk_status`       int(11)  NOT NULL,
    `parecer_fiscal`  text     DEFAULT NULL,
    `created_at`      datetime DEFAULT NULL,
    `updated_at`      datetime DEFAULT NULL,
    `deleted_at`      datetime DEFAULT NULL,
    primary key (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `supressao_relatorio`
--

INSERT INTO `supressao_relatorio` (`id`, `fk_servico`, `fk_resultado`, `nome_relatorio`, `conclusao`, `sobre_relatorio`,
                                   `fk_status`, `parecer_fiscal`, `created_at`, `updated_at`, `deleted_at`)
VALUES (1, 74, 2, 'Teste', '', '', 1, NULL, '2023-11-14 18:41:12', '2023-11-14 18:41:12', NULL),
       (2, 127, 5, 'Relatório de Supressão ASV nº 2024.5.2023.86702', '', '', 1, NULL, '2024-04-12 14:33:14',
        '2024-04-12 14:33:14', NULL);


CREATE TABLE `supressao_relatorio_anexos`
(
    `id`              int(11) NOT NULL AUTO_INCREMENT,
    `fk_relatorio`    int(11) NOT NULL,
    `nome_arquivo`    text    NOT NULL,
    `caminho_arquivo` text    NOT NULL,
    `created_at`      datetime DEFAULT NULL,
    `updated_at`      datetime DEFAULT NULL,
    `deleted_at`      datetime DEFAULT NULL,
    primary key (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

CREATE TABLE `programas_tipo`
(
    `id`         int(11) NOT NULL AUTO_INCREMENT,
    `chave`      varchar(255) DEFAULT NULL,
    `nome`       varchar(100) DEFAULT NULL,
    `created_at` datetime     DEFAULT NULL,
    `updated_at` datetime     DEFAULT NULL,
    `deleted_at` datetime     DEFAULT NULL,
    primary key (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `programas_tipo`
--

INSERT INTO `programas_tipo` (`id`, `chave`, `nome`, `created_at`, `updated_at`, `deleted_at`)
VALUES (1, '36a26b926d723984100b0b4fa85c96f7', 'Execução de Programas Ambientais', '2023-02-10 14:09:00', NULL, NULL),
       (2, 'cb243de0d9bad313330a439c0c20106f', 'Supervisão Ambiental', '2023-02-10 14:09:00', NULL, NULL);
