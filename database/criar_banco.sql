CREATE DATABASE IF NOT EXISTS sistema;
USE sistema;

DROP TABLE IF EXISTS usuario;

CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    tipo ('admin', 'user') DEFAULT 'user'
);

--Senha original: admin123
INSERT INTO usuario (email, senha_hash, tipo)
VALUES (
    'admin@admin.com',
    'Admin123!',
    'admin'
);
