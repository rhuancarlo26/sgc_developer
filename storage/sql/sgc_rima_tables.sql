-- SGC RIMA - scripts MySQL (sem migrations)
-- Observacao: ajuste o tipo de chave (BIGINT/INT) caso suas tabelas base usem outro padrao.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS sgc_rima (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_contrato INT NOT NULL,
    cod_emp VARCHAR(255) NOT NULL,
    id_campanha INT UNSIGNED NOT NULL,
    sei_dnit VARCHAR(255) NULL,
    subproduto VARCHAR(255) NOT NULL,
    modulo_id BIGINT UNSIGNED NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Em elaboração',
    planilha_nome VARCHAR(255) NULL,
    planilha_caminho VARCHAR(500) NULL,
    versao_analise INT UNSIGNED NOT NULL DEFAULT 1,
    aprovado_por INT NULL,
    data_aprovacao DATETIME NULL,
    arquivada_em DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_sgc_rima_contrato (id_contrato),
    KEY idx_sgc_rima_cod_emp (cod_emp),
    KEY idx_sgc_rima_status (status),
    KEY idx_sgc_rima_modulo (modulo_id),
    KEY idx_sgc_rima_aprovado_por (aprovado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sgc_rima_fotos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sgc_rima_id BIGINT UNSIGNED NOT NULL,
    nome_arquivo VARCHAR(255) NULL,
    caminho_arquivo VARCHAR(500) NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    data_captura DATETIME NULL,
    descricao TEXT NULL,
    metadados JSON NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_sgc_rima_fotos_rima (sgc_rima_id),
    CONSTRAINT fk_sgc_rima_fotos_rima
        FOREIGN KEY (sgc_rima_id) REFERENCES sgc_rima(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sgc_rima_anexos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sgc_rima_id BIGINT UNSIGNED NOT NULL,
    nome_arquivo VARCHAR(255) NULL,
    caminho_arquivo VARCHAR(500) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_sgc_rima_anexos_rima (sgc_rima_id),
    CONSTRAINT fk_sgc_rima_anexos_rima
        FOREIGN KEY (sgc_rima_id) REFERENCES sgc_rima(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sgc_rima_analises (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_contrato INT NOT NULL,
    id_campanha BIGINT UNSIGNED NOT NULL,
    versao_analise INT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL,
    observacoes TEXT NULL,
    fiscal_id INT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_sgc_rima_analises_contrato (id_contrato),
    KEY idx_sgc_rima_analises_campanha (id_campanha),
    KEY idx_sgc_rima_analises_fiscal (fiscal_id),
    KEY idx_sgc_rima_analises_status (status),
    CONSTRAINT fk_sgc_rima_analises_campanha
        FOREIGN KEY (id_campanha) REFERENCES sgc_rima(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FKs externas opcionais (cria apenas se tabelas/colunas existirem com tipo esperado)
-- Isso evita erro 150 em bases legadas com estrutura diferente.

SET @db = DATABASE();

-- sgc_rima.modulo_id -> sgc_modulos.id
SET @has_sgc_modulos = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'sgc_modulos'
      AND COLUMN_NAME = 'id'
      AND COLUMN_TYPE = 'bigint(20) unsigned'
);
SET @sql = IF(
    @has_sgc_modulos = 1,
    'ALTER TABLE sgc_rima ADD CONSTRAINT fk_sgc_rima_modulo FOREIGN KEY (modulo_id) REFERENCES sgc_modulos(id) ON UPDATE CASCADE ON DELETE SET NULL',
    'SELECT "SKIP fk_sgc_rima_modulo: tabela/coluna sgc_modulos.id ausente ou tipo diferente de BIGINT UNSIGNED" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sgc_rima.id_contrato -> contratos.id
SET @has_contratos = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'contratos'
      AND COLUMN_NAME = 'id'
    AND COLUMN_TYPE IN ('int', 'int(11)')
);
SET @sql = IF(
    @has_contratos = 1,
    'ALTER TABLE sgc_rima ADD CONSTRAINT fk_sgc_rima_contrato FOREIGN KEY (id_contrato) REFERENCES contratos(id) ON UPDATE CASCADE ON DELETE RESTRICT',
    'SELECT "SKIP fk_sgc_rima_contrato: tabela/coluna contratos.id ausente ou tipo diferente de INT" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sgc_rima.aprovado_por -> users.id
SET @has_users = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'id'
    AND COLUMN_TYPE IN ('int', 'int(11)')
);
SET @sql = IF(
    @has_users = 1,
    'ALTER TABLE sgc_rima ADD CONSTRAINT fk_sgc_rima_aprovado_por FOREIGN KEY (aprovado_por) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL',
    'SELECT "SKIP fk_sgc_rima_aprovado_por: tabela/coluna users.id ausente ou tipo diferente de INT" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sgc_rima_analises.id_contrato -> contratos.id
SET @has_contratos = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'contratos'
      AND COLUMN_NAME = 'id'
    AND COLUMN_TYPE IN ('int', 'int(11)')
);
SET @sql = IF(
    @has_contratos = 1,
    'ALTER TABLE sgc_rima_analises ADD CONSTRAINT fk_sgc_rima_analises_contrato FOREIGN KEY (id_contrato) REFERENCES contratos(id) ON UPDATE CASCADE ON DELETE RESTRICT',
    'SELECT "SKIP fk_sgc_rima_analises_contrato: tabela/coluna contratos.id ausente ou tipo diferente de INT" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sgc_rima_analises.fiscal_id -> users.id
SET @sql = IF(
    @has_users = 1,
    'ALTER TABLE sgc_rima_analises ADD CONSTRAINT fk_sgc_rima_analises_fiscal FOREIGN KEY (fiscal_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL',
    'SELECT "SKIP fk_sgc_rima_analises_fiscal: tabela/coluna users.id ausente ou tipo diferente de INT" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;
