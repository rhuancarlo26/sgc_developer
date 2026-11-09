<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Install the SGC Fauna schema without adopting or modifying existing tables.
     * Existing installations require a schema comparison before deployment.
     */
    public function up(): void
    {
        $definitions = $this->definitions();
        $existing = array_filter(array_keys($definitions), fn ($table) => Schema::hasTable($table));
        if ($existing) {
            throw new RuntimeException('SGC Fauna: comparar as tabelas existentes antes de instalar: ' . implode(', ', $existing));
        }

        foreach ($definitions as $sql) {
            DB::unprepared($sql);
        }
    }

    public function down(): void
    {
        foreach (array_reverse(array_keys($this->definitions())) as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function definitions(): array
    {
        return [
            'sgc_entregas_simplificadas' => <<<'SQL'
CREATE TABLE `sgc_entregas_simplificadas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) NOT NULL,
  `produto_tipo` varchar(30) NOT NULL,
  `entidade_tipo` varchar(100) NOT NULL,
  `entidade_id` bigint(20) unsigned NOT NULL,
  `modelo_id` bigint(20) unsigned DEFAULT NULL,
  `planilha_nome` varchar(255) DEFAULT NULL,
  `planilha_caminho` varchar(500) DEFAULT NULL,
  `status` enum('Em elaboração','Em análise','Aprovada','Rejeitada','Arquivada') NOT NULL DEFAULT 'Em elaboração',
  `versao_analise` int(10) unsigned NOT NULL DEFAULT 1,
  `aprovado_por` int(11) DEFAULT NULL,
  `data_aprovacao` datetime DEFAULT NULL,
  `arquivada_em` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_entrega_simplificada_origem` (`entidade_tipo`,`entidade_id`),
  KEY `idx_entrega_simplificada_contrato` (`id_contrato`),
  KEY `idx_entrega_simplificada_produto` (`produto_tipo`),
  KEY `idx_entrega_simplificada_status` (`status`),
  KEY `idx_entrega_simplificada_modelo` (`modelo_id`),
  KEY `idx_entrega_simplificada_aprovador` (`aprovado_por`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_analise_etapas' => <<<'SQL'
CREATE TABLE `sgc_fauna_analise_etapas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_contrato` bigint(20) unsigned NOT NULL,
  `id_campanha` bigint(20) unsigned NOT NULL,
  `etapa` varchar(255) NOT NULL,
  `analise` int(11) DEFAULT NULL,
  `status` enum('Em análise','Aprovada','Rejeitada') NOT NULL DEFAULT 'Em análise',
  `comentario` text DEFAULT NULL,
  `fiscal_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_anexos' => <<<'SQL'
CREATE TABLE `sgc_fauna_anexos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_contrato` bigint(20) unsigned NOT NULL,
  `id_campanha` bigint(20) unsigned NOT NULL,
  `tipo_anexo` enum('anuencia_proprietarios','registro_fotografico','dados_secundarios','art','ret','cr','ctf','anuencia_colecoes','oficio_atividades_campo') NOT NULL,
  `nome` varchar(255) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `versao` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_atropelamento_campanhas' => <<<'SQL'
CREATE TABLE `sgc_fauna_atropelamento_campanhas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campanha_id` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `rodovia` varchar(255) DEFAULT NULL,
  `data_inicial` date DEFAULT NULL,
  `data_final` date DEFAULT NULL,
  `uf_inicial` char(2) DEFAULT NULL,
  `uf_final` char(2) DEFAULT NULL,
  `km_inicial` decimal(10,3) DEFAULT NULL,
  `km_final` decimal(10,3) DEFAULT NULL,
  `latitude_inicial` decimal(10,7) DEFAULT NULL,
  `longitude_inicial` decimal(10,7) DEFAULT NULL,
  `latitude_final` decimal(10,7) DEFAULT NULL,
  `longitude_final` decimal(10,7) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sgc_fauna_atropelamento_campanhas_campanha_id_index` (`campanha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_campanha' => <<<'SQL'
CREATE TABLE `sgc_fauna_campanha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) DEFAULT NULL,
  `id_campanha` int(11) DEFAULT NULL,
  `sei_dnit` int(11) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `arquivada_em` timestamp NULL DEFAULT NULL,
  `versao_analise` int(11) DEFAULT NULL,
  `planilha_atropelamento` varchar(255) DEFAULT NULL,
  `consideracoes_atropelamento` varchar(2555) DEFAULT NULL,
  `data_ini` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `periodo` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `cod_emp` varchar(255) DEFAULT NULL,
  `subproduto` varchar(999) DEFAULT NULL,
  `modo_preenchimento` enum('completo','simplificado') NOT NULL DEFAULT 'completo',
  `etapa_atual` varchar(20) DEFAULT NULL COMMENT 'Progresso do rascunho: apresentacao | metodologia | resultados | anexos',
  `updated_at` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sgc_fauna_campanha_modo` (`modo_preenchimento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_campanha_abios' => <<<'SQL'
CREATE TABLE `sgc_fauna_campanha_abios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contrato_id` bigint(20) unsigned NOT NULL,
  `campanha_id` bigint(20) unsigned NOT NULL,
  `n_abio` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_campanha_profissionais' => <<<'SQL'
CREATE TABLE `sgc_fauna_campanha_profissionais` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campanha_id` bigint(20) unsigned NOT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `profissional_id` bigint(20) unsigned NOT NULL,
  `grupo_faunistico` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_amostragem_cavernicola' => <<<'SQL'
CREATE TABLE `sgc_fauna_amostragem_cavernicola` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) DEFAULT NULL,
  `id_campanha` int(11) DEFAULT NULL,
  `cavidade` text DEFAULT NULL,
  `latitude` text DEFAULT NULL,
  `longitude` text DEFAULT NULL,
  `distancia_eixo_rodovia` text DEFAULT NULL,
  `formacao_associada` text DEFAULT NULL,
  `temperatura_media_interna` text DEFAULT NULL,
  `temperatura_media_externa` text DEFAULT NULL,
  `umidade_relativa_interna` text DEFAULT NULL,
  `umidade_relativa_externa` text DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_comentarios' => <<<'SQL'
CREATE TABLE `sgc_fauna_comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) DEFAULT NULL,
  `campanha_id` int(11) DEFAULT NULL,
  `analise` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `etapa` text DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_metodologia' => <<<'SQL'
CREATE TABLE `sgc_fauna_metodologia` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campanha_id` bigint(20) unsigned NOT NULL,
  `id_modulo` varchar(45) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `grupo_faunistico` varchar(255) DEFAULT NULL,
  `metodologia` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_modulos_amostrais' => <<<'SQL'
CREATE TABLE `sgc_fauna_modulos_amostrais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) DEFAULT NULL,
  `chave` varchar(255) NOT NULL,
  `campanha_id` int(11) DEFAULT NULL,
  `id_servico` int(11) DEFAULT NULL,
  `tamanho_modulo` int(11) DEFAULT NULL,
  `data_cadastro` varchar(20) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `municipio` varchar(50) DEFAULT NULL,
  `bioma` varchar(30) DEFAULT NULL,
  `latitude_inicial` varchar(30) DEFAULT NULL,
  `longitude_inicial` varchar(30) DEFAULT NULL,
  `zona_inicial` varchar(30) DEFAULT NULL,
  `latitude_final` varchar(30) DEFAULT NULL,
  `longitude_final` varchar(30) DEFAULT NULL,
  `zona_final` varchar(30) DEFAULT NULL,
  `fitofisionomia` text DEFAULT NULL,
  `nome_arquivo` varchar(200) DEFAULT NULL,
  `shape_file` text DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `local_shape` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
SQL,
            'sgc_fauna_profissionais' => <<<'SQL'
CREATE TABLE `sgc_fauna_profissionais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) DEFAULT NULL,
  `profissional` varchar(255) DEFAULT NULL,
  `formacao` varchar(255) DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `cpf` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `curriculum_lattes` text DEFAULT NULL,
  `funcao` text DEFAULT NULL,
  `ctf` text DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `conselho_de_classe` text DEFAULT NULL,
  `numero_de_registro` int(11) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_amostragem_quelo_crocod' => <<<'SQL'
CREATE TABLE `sgc_fauna_amostragem_quelo_crocod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) DEFAULT NULL,
  `id_campanha` int(11) DEFAULT NULL,
  `ponto_de_coleta` text DEFAULT NULL,
  `nome_curso_hidrico` text DEFAULT NULL,
  `latitude` text DEFAULT NULL,
  `longitude` text DEFAULT NULL,
  `coordenadas` text DEFAULT NULL,
  `bacia_hidrografica` text DEFAULT NULL,
  `profundidade` text DEFAULT NULL,
  `largura` text DEFAULT NULL,
  `tipo_substrato` text DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_resultados' => <<<'SQL'
CREATE TABLE `sgc_fauna_resultados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_campanha` bigint(20) unsigned DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `modulo` varchar(255) DEFAULT NULL,
  `parcela` varchar(255) DEFAULT NULL,
  `id_armadilha` int(11) DEFAULT NULL,
  `grupo_amostrado` varchar(255) DEFAULT NULL,
  `data_registro` date DEFAULT NULL,
  `hora_registro` time DEFAULT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `classe` varchar(255) DEFAULT NULL,
  `ordem` varchar(255) DEFAULT NULL,
  `familia` varchar(255) DEFAULT NULL,
  `genero` varchar(255) DEFAULT NULL,
  `especie` varchar(255) DEFAULT NULL,
  `nome_comum` varchar(255) DEFAULT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `faixa_etaria` varchar(255) DEFAULT NULL,
  `qnt_individuos` int(11) DEFAULT NULL,
  `num_marcacao` varchar(255) DEFAULT NULL,
  `coletado` varchar(255) DEFAULT NULL,
  `num_tombamento` varchar(255) DEFAULT NULL,
  `dados_biometricos` text DEFAULT NULL,
  `comp_total` int(11) DEFAULT NULL,
  `cabeca` int(11) DEFAULT NULL,
  `cauda` int(11) DEFAULT NULL,
  `femur` int(11) DEFAULT NULL,
  `orelha` int(11) DEFAULT NULL,
  `peso` int(11) DEFAULT NULL,
  `status_conservacao_federal` varchar(255) DEFAULT NULL,
  `status_conservacao_iucn` varchar(255) DEFAULT NULL,
  `especies_bioindicadoras` text DEFAULT NULL,
  `especies_alvo_monitoramento` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_resultados_aquatica' => <<<'SQL'
CREATE TABLE `sgc_fauna_resultados_aquatica` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_campanha` bigint(20) unsigned NOT NULL,
  `id_contrato` bigint(20) unsigned NOT NULL,
  `campanha` varchar(255) DEFAULT NULL,
  `estacao_do_ano` varchar(100) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `periodo` varchar(255) DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `condicao_climatica` varchar(255) DEFAULT NULL,
  `temperatura` decimal(5,2) DEFAULT NULL,
  `pluviosidade` decimal(5,2) DEFAULT NULL,
  `municipio` varchar(255) DEFAULT NULL,
  `unidade_amostral` varchar(255) DEFAULT NULL,
  `ponto_amostral` varchar(255) DEFAULT NULL,
  `datum` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `metodologia` varchar(255) DEFAULT NULL,
  `tipo_metodologia` varchar(255) DEFAULT NULL,
  `fitofisionomia` varchar(255) DEFAULT NULL,
  `habitat_preferencial` varchar(255) DEFAULT NULL,
  `tipo_ambiente` varchar(255) DEFAULT NULL,
  `largura_media_rio` decimal(7,2) DEFAULT NULL,
  `profundidade_media` decimal(7,2) DEFAULT NULL,
  `tipo_substrato` varchar(255) DEFAULT NULL,
  `caracteristicas_agua` text DEFAULT NULL,
  `caracteristicas_entorno_ponto` text DEFAULT NULL,
  `classe` varchar(255) DEFAULT NULL,
  `ordem` varchar(255) DEFAULT NULL,
  `familia` varchar(255) DEFAULT NULL,
  `genero` varchar(255) DEFAULT NULL,
  `especie` varchar(255) DEFAULT NULL,
  `nome_cientifico` varchar(255) DEFAULT NULL,
  `nome_comum` varchar(255) DEFAULT NULL,
  `abundancia` int(11) DEFAULT NULL,
  `numero_de_registros` int(11) DEFAULT NULL,
  `migratoria` varchar(255) DEFAULT NULL,
  `sensibilidade` varchar(255) DEFAULT NULL,
  `endemismo` varchar(255) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `iucn` varchar(255) DEFAULT NULL,
  `mma` varchar(255) DEFAULT NULL,
  `salve` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `pan_prim` varchar(255) DEFAULT NULL,
  `registro_fotografico` varchar(255) DEFAULT NULL,
  `coletado` varchar(50) DEFAULT NULL,
  `numero_tombo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_campanha` (`id_campanha`),
  KEY `idx_contrato` (`id_contrato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_resultados_cavernicola' => <<<'SQL'
CREATE TABLE `sgc_fauna_resultados_cavernicola` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_campanha` bigint(20) unsigned NOT NULL,
  `id_contrato` bigint(20) unsigned NOT NULL,
  `caverna` varchar(255) DEFAULT NULL,
  `campanha` varchar(255) DEFAULT NULL,
  `estacao_do_ano` varchar(100) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `periodo` varchar(255) DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `condicao_climatica` varchar(255) DEFAULT NULL,
  `temperatura` decimal(5,2) DEFAULT NULL,
  `pluviosidade` decimal(5,2) DEFAULT NULL,
  `municipio` varchar(255) DEFAULT NULL,
  `unidade_amostral` varchar(255) DEFAULT NULL,
  `ponto_amostral` varchar(255) DEFAULT NULL,
  `datum` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `metodologia` varchar(255) DEFAULT NULL,
  `tipo_metodologia` varchar(255) DEFAULT NULL,
  `fitofisionomia` varchar(255) DEFAULT NULL,
  `substrato_amostrado` varchar(255) DEFAULT NULL,
  `caracteristicas_entorno_ponto` text DEFAULT NULL,
  `classe` varchar(255) DEFAULT NULL,
  `ordem` varchar(255) DEFAULT NULL,
  `familia` varchar(255) DEFAULT NULL,
  `genero` varchar(255) DEFAULT NULL,
  `especie` varchar(255) DEFAULT NULL,
  `nome_cientifico` varchar(255) DEFAULT NULL,
  `nome_comum` varchar(255) DEFAULT NULL,
  `abundancia` int(11) DEFAULT NULL,
  `numero_de_registros` int(11) DEFAULT NULL,
  `categoria_ecologica` varchar(255) DEFAULT NULL,
  `migratoria` varchar(255) DEFAULT NULL,
  `sensibilidade` varchar(255) DEFAULT NULL,
  `endemismo` varchar(255) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `presenca_guano` varchar(50) DEFAULT NULL,
  `presenca_agua` varchar(50) DEFAULT NULL,
  `conectividade_externa` varchar(255) DEFAULT NULL,
  `perturbacao_antropica` varchar(255) DEFAULT NULL,
  `iucn` varchar(255) DEFAULT NULL,
  `mma` varchar(255) DEFAULT NULL,
  `salve` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `pan_prim` text DEFAULT NULL,
  `registro_fotografico` varchar(255) DEFAULT NULL,
  `coletado` varchar(50) DEFAULT NULL,
  `numero_tombo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_campanha` (`id_campanha`),
  KEY `idx_contrato` (`id_contrato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_resultados_consideracoes' => <<<'SQL'
CREATE TABLE `sgc_fauna_resultados_consideracoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campanha` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `consideracoes` text DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_fauna_resultados_terrestre' => <<<'SQL'
CREATE TABLE `sgc_fauna_resultados_terrestre` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_campanha` bigint(20) unsigned NOT NULL,
  `id_contrato` bigint(20) unsigned NOT NULL,
  `campanha` varchar(255) DEFAULT NULL,
  `estacao_do_ano` varchar(100) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `periodo` text DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `condicao_climatica` varchar(255) DEFAULT NULL,
  `temperatura` decimal(5,2) DEFAULT NULL,
  `pluviosidade` decimal(5,2) DEFAULT NULL,
  `municipio` varchar(255) DEFAULT NULL,
  `unidade_amostral` varchar(255) DEFAULT NULL,
  `ponto_amostral` varchar(255) DEFAULT NULL,
  `datum` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `metodologia` varchar(255) DEFAULT NULL,
  `tipo_metodologia` varchar(255) DEFAULT NULL,
  `fitofisionomia` varchar(255) DEFAULT NULL,
  `habitat` varchar(255) DEFAULT NULL,
  `caracteristicas_ponto` varchar(255) DEFAULT NULL,
  `classe` varchar(255) DEFAULT NULL,
  `ordem` varchar(255) DEFAULT NULL,
  `familia` varchar(255) DEFAULT NULL,
  `genero` varchar(255) DEFAULT NULL,
  `especie` varchar(255) DEFAULT NULL,
  `nome_cientifico` varchar(255) DEFAULT NULL,
  `nome_comum` varchar(255) DEFAULT NULL,
  `abundancia` int(11) DEFAULT NULL,
  `numero_de_registros` int(11) DEFAULT NULL,
  `migratoria` varchar(255) DEFAULT NULL,
  `sensibilidade` varchar(255) DEFAULT NULL,
  `endemismo` varchar(255) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `iucn` varchar(255) DEFAULT NULL,
  `mma` varchar(255) DEFAULT NULL,
  `salve` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `pan_prim` varchar(255) DEFAULT NULL,
  `registro_fotografico` varchar(255) DEFAULT NULL,
  `coletado` varchar(255) DEFAULT NULL,
  `numero_tombo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            'sgc_modulos' => <<<'SQL'
CREATE TABLE `sgc_modulos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `nome_planilha_modelo` varchar(255) DEFAULT NULL,
  `caminho_planilha_modelo` varchar(255) DEFAULT NULL,
  `campos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`campos`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_produto_abios' => <<<'SQL'
CREATE TABLE `sgc_produto_abios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(255) NOT NULL,
  `id_campanha` int(11) NOT NULL,
  `id_abio` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fauna_exec_campanhas_abio_id_campanha_foreign` (`id_campanha`),
  KEY `fauna_exec_campanhas_abio_id_abio_foreign` (`id_abio`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
SQL,
            'sgc_entrega_simplificada_analises' => <<<'SQL'
CREATE TABLE `sgc_entrega_simplificada_analises` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entrega_simplificada_id` bigint(20) unsigned NOT NULL,
  `versao_analise` int(10) unsigned NOT NULL,
  `status` enum('Aprovada','Rejeitada') NOT NULL,
  `observacoes` text DEFAULT NULL,
  `fiscal_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_entrega_analise_versao` (`entrega_simplificada_id`,`versao_analise`),
  KEY `idx_entrega_analise_fiscal` (`fiscal_id`),
  KEY `idx_entrega_analise_status` (`status`),
  CONSTRAINT `fk_entrega_simplificada_analise_entrega` FOREIGN KEY (`entrega_simplificada_id`) REFERENCES `sgc_entregas_simplificadas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_entrega_simplificada_arquivos' => <<<'SQL'
CREATE TABLE `sgc_entrega_simplificada_arquivos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entrega_simplificada_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('foto','anexo') NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `caminho_arquivo` varchar(500) NOT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `tamanho_bytes` bigint(20) unsigned DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `data_captura` datetime DEFAULT NULL,
  `metadados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadados`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entrega_arquivo_entrega` (`entrega_simplificada_id`),
  KEY `idx_entrega_arquivo_tipo` (`tipo`),
  CONSTRAINT `fk_entrega_simplificada_arquivo_entrega` FOREIGN KEY (`entrega_simplificada_id`) REFERENCES `sgc_entregas_simplificadas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
            'sgc_fauna_entrega_simplificada_planilhas' => <<<'SQL'
CREATE TABLE `sgc_fauna_entrega_simplificada_planilhas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entrega_simplificada_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('terrestre','aquatica','cavernicola') NOT NULL,
  `modulo_id` bigint(20) unsigned NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `caminho_arquivo` varchar(500) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `tamanho_bytes` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_fauna_entrega_simplificada_planilha_tipo` (`entrega_simplificada_id`,`tipo`),
  KEY `idx_fauna_entrega_simplificada_planilha_modulo` (`modulo_id`),
  CONSTRAINT `fk_fauna_entrega_simplificada_planilha_entrega` FOREIGN KEY (`entrega_simplificada_id`) REFERENCES `sgc_entregas_simplificadas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fauna_entrega_simplificada_planilha_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `sgc_modulos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
        ];
    }
};
