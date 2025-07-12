<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Atualizar ENUM da tabela users
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'empresa', 'prestador', 'professor') DEFAULT 'empresa'");
    echo "✅ Campo 'role' da tabela 'users' atualizado!\n";
    
    // Adicionar campo user_level na tabela empresas se não existir
    $stmt = $pdo->query("SHOW COLUMNS FROM empresas LIKE 'user_level'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        $pdo->exec("ALTER TABLE empresas ADD COLUMN user_level ENUM('admin', 'empresa', 'prestador') DEFAULT 'empresa' AFTER estado");
        echo "✅ Campo 'user_level' adicionado na tabela 'empresas'!\n";
    } else {
        echo "ℹ️ Campo 'user_level' já existe na tabela 'empresas'.\n";
    }
    
    // Atualizar todas as empresas existentes para ter nível 'empresa'
    $pdo->exec("UPDATE empresas SET user_level = 'empresa' WHERE user_level IS NULL OR user_level = ''");
    echo "✅ Empresas existentes atualizadas para nível 'empresa'!\n";
    
    // Atualizar usuário admin para ter nível 'admin'
    $pdo->exec("UPDATE users SET role = 'admin' WHERE email = 'admin@protalent.com'");
    echo "✅ Usuário admin atualizado!\n";
    
    echo "✅ Sistema de níveis de usuário atualizado com sucesso!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro ao atualizar banco de dados: " . $e->getMessage() . "\n";
}
?> 