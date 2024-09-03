CREATE TABLE `recurso_veiculo_quilometragens`
(
    `id`                 BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `contrato_id`        BIGINT(20) UNSIGNED NOT NULL,
    `recurso_veiculo_id` BIGINT(20) UNSIGNED NOT NULL,
    `km_referencia`      DOUBLE              NOT NULL,
    `mes_referencia`     DATE                NOT NULL,
    `created_at`         TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`         TIMESTAMP           NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);


CREATE TABLE `at_fauna_config_vinculacao`
(
    `id`         int(11) NOT NULL AUTO_INCREMENT,
    `fk_licenca` int(11) NOT NULL,
    `fk_servico` int(11) NOT NULL,
    `shapefile`  text     DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `at_fauna_config_vinculacao`
--

INSERT INTO `at_fauna_config_vinculacao` (`id`, `fk_licenca`, `fk_servico`, `shapefile`, `created_at`, `updated_at`,
                                          `deleted_at`)
VALUES (1, 27, 67, NULL, '2023-08-28 17:27:38', '2023-08-28 17:27:38', NULL),
       (2, 358, 113, NULL, '2024-03-20 13:39:40', '2024-03-20 13:39:40', NULL),
       (3, 403, 155, NULL, '2024-03-21 09:34:31', '2024-03-21 09:34:31', NULL),
       (4, 389, 136, NULL, '2024-03-22 15:01:21', '2024-03-22 15:01:21', NULL),
       (5, 20, 179, NULL, '2024-03-25 11:22:02', '2024-03-25 11:22:02', NULL),
       (7, 411, 205, NULL, '2024-07-15 12:28:59', '2024-07-15 12:28:59', NULL);

CREATE TABLE `at_fauna_config_vinculacao_ret`
(
    `id`                      int(11) NOT NULL AUTO_INCREMENT,
    `fk_at_config_vinculacao` int(11) NOT NULL,
    `caminho_arquivo`         text     DEFAULT NULL,
    `nome_arquivo`            text     DEFAULT NULL,
    `created_at`              datetime DEFAULT NULL,
    `updated_at`              datetime DEFAULT NULL,
    `deleted_at`              datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `at_fauna_config_vinculacao_ret`
--

INSERT INTO `at_fauna_config_vinculacao_ret` (`id`, `fk_at_config_vinculacao`, `caminho_arquivo`, `nome_arquivo`,
                                              `created_at`, `updated_at`, `deleted_at`)
VALUES (4, 1, 'uploads/atropelamento_fauna/rets/11954203930.pdf', 'ABIO RET - BR-230.pdf', '2023-08-28 18:17:39',
        '2023-08-28 18:17:39', NULL),
       (5, 1, 'uploads/atropelamento_fauna/rets/1482422773.pdf',
        'Declaração  EXCLUSÃO UNIMED PAPEL TIMBRADO - VINICIUS DA SILVA COELHO.pdf', '2023-11-03 14:23:12',
        '2023-11-03 14:23:12', NULL),
       (6, 2, 'uploads/atropelamento_fauna/rets/21477686793.pdf',
        'RET_12101_11081486650919066386778332332512385834.pdf', '2024-03-20 13:43:15', '2024-03-20 13:43:15', NULL),
       (7, 3, 'uploads/atropelamento_fauna/rets/3295540494.pdf', 'RET 01-2022.pdf', '2024-03-21 09:34:47',
        '2024-03-21 09:34:47', NULL),
       (8, 5, 'uploads/atropelamento_fauna/rets/52071858093.pdf', 'Lista.pdf', '2024-04-02 11:21:41',
        '2024-04-02 11:21:41', NULL),
       (9, 7, 'uploads/atropelamento_fauna/rets/71372129081.pdf', 'RAA_025_2022_atualizada.pdf', '2024-07-15 12:29:22',
        '2024-07-15 12:29:22', NULL);


CREATE TABLE `at_malha_viaria_shapefile`
(
    `id`                 int(11)  NOT NULL AUTO_INCREMENT,
    `fk_servico_licenca` tinytext NOT NULL,
    `shapefile`          text     NOT NULL,
    `created_at`         datetime     DEFAULT NULL,
    `updated_at`         datetime     DEFAULT NULL,
    `deleted_at`         datetime     DEFAULT NULL,
    `local_shape`        varchar(100) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `at_malha_viaria_shapefile`
--

INSERT INTO `at_malha_viaria_shapefile` (`id`, `fk_servico_licenca`, `shapefile`, `created_at`, `updated_at`,
                                         `deleted_at`, `local_shape`)
VALUES (3, '49', '', '2024-03-20 15:22:15', '2024-03-20 15:22:15', NULL, NULL),
       (4, '49', '', '2024-03-28 17:13:09', '2024-03-28 17:13:09', NULL,
        'F:\\Apache24_8081\\htdocs\\DPP\\ecosistema\\writable\\\\file_shape\\6605cf54dfd81.json');


CREATE TABLE `at_fauna_parecer_configuracao`
(
    `id`         int(11) NOT NULL AUTO_INCREMENT,
    `fk_servico` int(11)  DEFAULT NULL,
    `parecer`    text    NOT NULL,
    `fk_status`  int(11)  DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `at_fauna_parecer_configuracao`
--

INSERT INTO `at_fauna_parecer_configuracao` (`id`, `fk_servico`, `parecer`, `fk_status`, `created_at`, `updated_at`,
                                             `deleted_at`)
VALUES (1, 67, 'aprovado', 3, '2023-08-09 19:29:49', '2023-08-28 19:22:58', NULL),
       (2, 113, 'Ok.', 3, '2024-03-14 16:35:22', '2024-04-01 13:51:59', NULL),
       (3, 136, 'Faltam os shapefiles. Restante está OK.', 3, '2024-03-20 15:59:14', '2024-03-25 10:42:11', NULL),
       (4, 155, '', 1, '2024-03-21 09:33:57', '2024-03-21 09:33:57', NULL),
       (5, 141, '', 1, '2024-03-21 16:35:10', '2024-03-21 16:35:10', NULL),
       (6, 179, 'teste teste', 3, '2024-03-25 11:18:12', '2024-04-02 11:13:33', NULL),
       (7, 142, '', 1, '2024-05-16 10:33:23', '2024-05-16 10:33:23', NULL),
       (8, 280, '', 1, '2024-06-04 22:05:01', '2024-06-04 22:05:01', NULL),
       (9, 205, '', 1, '2024-07-12 15:36:51', '2024-07-12 15:36:51', NULL),
       (10, 311, '', 1, '2024-07-18 10:45:01', '2024-07-18 10:45:01', NULL);



--
-- Table structure for table `at_fauna_execucao_campanhas`
--

CREATE TABLE `at_fauna_execucao_campanhas`
(
    `id`                 int(11) NOT NULL AUTO_INCREMENT,
    `fk_servico_licenca` int(11) NOT NULL,
    `uf_inicial`         int(11) NOT NULL,
    `uf_final`           int(11) NOT NULL,
    `km_inicial`         text        DEFAULT NULL,
    `km_final`           text        DEFAULT NULL,
    `latitude_inicial`   text        DEFAULT NULL,
    `latitude_final`     text        DEFAULT NULL,
    `longitude_inicial`  text        DEFAULT NULL,
    `longitude_final`    text        DEFAULT NULL,
    `data_inicial`       text        DEFAULT NULL,
    `data_final`         text        DEFAULT NULL,
    `periodo`            varchar(45) DEFAULT NULL,
    `observacao`         text        DEFAULT NULL,
    `zona_inicial`       varchar(45) DEFAULT NULL,
    `zona_final`         varchar(45) DEFAULT NULL,
    `created_at`         datetime    DEFAULT NULL,
    `updated_at`         datetime    DEFAULT NULL,
    `deleted_at`         datetime    DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `at_fauna_execucao_campanhas`
--

INSERT INTO `at_fauna_execucao_campanhas` (`id`, `fk_servico_licenca`, `uf_inicial`, `uf_final`, `km_inicial`,
                                           `km_final`, `latitude_inicial`, `latitude_final`, `longitude_inicial`,
                                           `longitude_final`, `data_inicial`, `data_final`, `periodo`, `observacao`,
                                           `zona_inicial`, `zona_final`, `created_at`, `updated_at`, `deleted_at`)
VALUES (1, 21, 14, 14, '0', '986', '390533', '697431', '9041395', '9491456', '2023-01-17', '2023-04-25', NULL,
        'Apenas um teste para averiguar se o sistema está funcionando adequadamente ou necessita de correções relacionadas ao banco de dados ou layout da página. 28/08/2023',
        NULL, NULL, '2023-08-28 18:28:27', '2023-08-28 18:29:02', NULL),
       (2, 21, 14, 14, '0', '984', '1', '1', '1', '1', '2023-09-12', '2023-09-22', NULL, NULL, NULL, NULL,
        '2023-10-10 16:27:01', '2023-10-10 16:27:01', NULL),
       (3, 21, 14, 14, '0', '984', '984', '111', '111', '111', '2023-11-13', '2023-11-24', NULL, 'teste', NULL, NULL,
        '2023-11-13 17:11:25', '2023-11-13 17:11:25', NULL),
       (4, 297, 24, 24, '0,0', '73,18', ' 26°52\'56.15\"S', ' 26°54\'35.92\"S', ' 48°39\'41.48\"O', ' 49°16\'40.46\"O',
        '2023-09-01', '2023-09-30', NULL, '-', NULL, NULL, '2024-03-25 14:41:39', '2024-03-25 14:41:39', NULL),
       (5, 297, 24, 24, '0,0', '73,18', '26°52\'56.15\"S ', '26°54\'35.92\"S', '48°39\'41.48\"O', '49°16\'40.46\"O',
        '2023-10-01', '2023-10-31', NULL, '-', NULL, NULL, '2024-03-25 14:42:38', '2024-03-25 14:42:38', NULL),
       (6, 297, 24, 24, '0,0', '73,18', '26°52\'56.15\"S', '26°54\'35.92\"S', '48°39\'41.48\"O', '49°16\'40.46\"O',
        '2023-11-01', '2023-11-30', NULL, '-', NULL, NULL, '2024-03-25 14:44:23', '2024-03-25 14:44:23', NULL),
       (7, 297, 24, 24, '0,0', '73,18', '26°52\'56.15\"S', '26°54\'35.92\"S', '48°39\'41.48\"O', '49°16\'40.46\"O',
        '2023-12-01', '2023-12-31', NULL, '-', NULL, NULL, '2024-03-25 14:47:38', '2024-03-25 14:47:38', NULL),
       (8, 297, 24, 24, '0,0', '73,18', '26°52\'56.15\"S', '26°54\'35.92\"S', '48°39\'41.48\"O', '49°16\'40.46\"O',
        '2024-01-01', '2024-01-31', NULL, NULL, NULL, NULL, '2024-03-25 14:48:24', '2024-03-25 14:48:24', NULL),
       (9, 297, 24, 24, '0,0', '73,18', '26°52\'56.15\"S', '26°54\'35.92\"S ', '48°39\'41.48\"O', '49°16\'40.46\"O',
        '2024-02-01', '2024-02-29', NULL, NULL, NULL, NULL, '2024-03-25 14:49:14', '2024-03-25 14:49:14', NULL),
       (10, 49, 23, 23, '300+540', '511+760', '-30,12364', '-31,63009', '-51,349114', '-52,32464', '2023-09-01',
        '2023-09-05', NULL,
        '52ª campanha de monitoramento veicular \n50ª campanha de monitoramento de por caminhamento (anfíbios)', NULL,
        NULL, '2024-03-28 17:23:12', '2024-03-28 17:25:12', NULL),
       (11, 319, 14, 14, '0', '30', '321654', '321654', '321654', '321654', '2024-04-02', '2024-04-19', NULL,
        'Observação teste', NULL, NULL, '2024-04-02 11:20:10', '2024-04-02 11:20:10', NULL),
       (12, 319, 14, 0, '30', '40', '123456', '123456', '123456', '123456', '2024-04-04', '2024-04-24', NULL,
        '321654897', NULL, NULL, '2024-04-04 15:25:58', '2024-04-04 15:25:58', NULL),
       (13, 319, 14, 14, '30', '40', '321654', '321654', '32165', '321654', '2024-04-04', '2024-04-25', NULL, '321654',
        NULL, NULL, '2024-04-04 15:26:29', '2024-04-04 15:26:29', NULL);

--
-- Table structure for table `at_fauna_execucao_campanha_abio`
--

CREATE TABLE `at_fauna_execucao_campanha_abio`
(
    `id`                   int(11) NOT NULL AUTO_INCREMENT,
    `fk_execucao_campanha` int(11) NOT NULL,
    `fk_config_vinculacao` int(11) NOT NULL,
    `created_at`           datetime DEFAULT NULL,
    `updated_at`           datetime DEFAULT NULL,
    `deleted_at`           datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `at_fauna_execucao_campanha_abio`
--

INSERT INTO `at_fauna_execucao_campanha_abio` (`id`, `fk_execucao_campanha`, `fk_config_vinculacao`, `created_at`,
                                               `updated_at`, `deleted_at`)
VALUES (1, 1, 1, '2023-08-28 18:28:34', '2023-08-28 18:28:34', NULL),
       (2, 3, 1, '2023-11-13 17:11:29', '2023-11-13 17:11:29', NULL),
       (4, 10, 2, '2024-03-28 17:25:03', '2024-03-28 17:25:03', NULL),
       (6, 13, 5, '2024-04-04 15:26:32', '2024-04-04 15:26:32', NULL);

-- Table structure for table `at_fauna_execucao_campanha_ret`
--

CREATE TABLE `at_fauna_execucao_campanha_ret`
(
    `id`                   int(11) NOT NULL AUTO_INCREMENT,
    `fk_execucao_campanha` int(11) NOT NULL,
    `fk_config_ret`        int(11) NOT NULL,
    `created_at`           datetime DEFAULT NULL,
    `updated_at`           datetime DEFAULT NULL,
    `deleted_at`           datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_general_ci;

--
-- Dumping data for table `at_fauna_execucao_campanha_ret`
--

INSERT INTO `at_fauna_execucao_campanha_ret` (`id`, `fk_execucao_campanha`, `fk_config_ret`, `created_at`, `updated_at`,
                                              `deleted_at`)
VALUES (1, 1, 4, '2023-08-28 18:28:46', '2023-08-28 18:28:46', NULL),
       (2, 3, 5, '2023-11-13 17:11:35', '2023-11-13 17:11:35', NULL),
       (3, 10, 6, '2024-03-28 17:25:06', '2024-03-28 17:25:06', NULL);
