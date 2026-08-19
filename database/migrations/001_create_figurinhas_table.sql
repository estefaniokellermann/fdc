CREATE TABLE IF NOT EXISTS figurinhas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    codigo VARCHAR(32) NOT NULL,
    nome VARCHAR(160) NOT NULL,
    selecao VARCHAR(120) NOT NULL DEFAULT '',
    edicao_album VARCHAR(80) NOT NULL,
    ano_copa SMALLINT UNSIGNED NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_figurinhas_codigo (codigo),
    KEY idx_figurinhas_edicao_album (edicao_album),
    CONSTRAINT chk_figurinhas_preco CHECK (preco_unitario >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
