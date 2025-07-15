-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS protalentappbr_protalent
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE protalentappbr_protalent;

-- Criação da tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'empresa', 'professor', 'talent') NOT NULL DEFAULT 'talent',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de talentos
CREATE TABLE IF NOT EXISTS talents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    curso VARCHAR(100),
    semestre INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de empresas
CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cnpj VARCHAR(18) UNIQUE NOT NULL,
    razao_social VARCHAR(200) NOT NULL,
    nome_fantasia VARCHAR(200),
    nome_mentor VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(20),
    cep VARCHAR(10),
    endereco VARCHAR(200),
    numero VARCHAR(10),
    complemento VARCHAR(100),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    user_level ENUM('admin', 'empresa', 'prestador') DEFAULT 'empresa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de professores
CREATE TABLE IF NOT EXISTS professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    whatsapp VARCHAR(20),
    instituicao VARCHAR(200),
    area_atuacao VARCHAR(200),
    interessado_edicoes_futuras BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de desafios
CREATE TABLE IF NOT EXISTS desafios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    mentor VARCHAR(100) NOT NULL,
    whatsapp VARCHAR(20),
    titulo VARCHAR(200) NOT NULL,
    descricao_problema TEXT NOT NULL,
    pesquisado BOOLEAN DEFAULT FALSE,
    descricao_pesquisa TEXT,
    nivel_trl VARCHAR(20),
    requisitos_especificos BOOLEAN DEFAULT FALSE,
    descricao_requisitos TEXT,
    status ENUM('ativo', 'em_andamento', 'concluido') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);

-- Inserir usuário admin padrão (senha: admin123) - apenas se não existir
INSERT IGNORE INTO users (nome, email, password, tipo) VALUES 
('Administrador', 'admin@protalent.com', '$2y$10$abcdefghijklmnopqrstuv1234567890abcdefghi1234567890abcdefghi', 'admin');

-- Inserir alguns talentos de exemplo - apenas se não existirem
INSERT IGNORE INTO talents (nome, email, phone, skills, experience_years, salary_expectation) VALUES 
('João Silva', 'joao@email.com', '(11) 99999-9999', 'PHP, MySQL, JavaScript, Laravel', 5, 8000.00),
('Maria Santos', 'maria@email.com', '(11) 88888-8888', 'React, Node.js, TypeScript', 3, 7000.00),
('Pedro Costa', 'pedro@email.com', '(11) 77777-7777', 'Python, Django, PostgreSQL', 4, 7500.00); 