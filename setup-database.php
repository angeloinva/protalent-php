<?php
// Script para configurar o banco de dados
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Ler o arquivo SQL
    $sql = file_get_contents('database/schema.sql');
    
    // Executar as queries
    $db->exec($sql);
    
    echo "Banco de dados configurado com sucesso!\n";
    echo "Tabelas criadas:\n";
    echo "- users\n";
    echo "- talents\n";
    echo "- empresas\n";
    echo "- professores\n";
    echo "- desafios\n";
    echo "\nUsuário admin criado:\n";
    echo "Email: admin@protalent.com\n";
    echo "Senha: admin123\n";
    
} catch (PDOException $e) {
    echo "Erro ao configurar banco de dados: " . $e->getMessage() . "\n";
}
?> 