<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Atualizando tabela de desafios...\n";
    
    // Adicionar coluna descricao_pesquisa se não existir
    $sql = "SHOW COLUMNS FROM desafios LIKE 'descricao_pesquisa'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE desafios ADD COLUMN descricao_pesquisa TEXT AFTER pesquisado";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        echo "✓ Coluna 'descricao_pesquisa' adicionada com sucesso!\n";
    } else {
        echo "✓ Coluna 'descricao_pesquisa' já existe.\n";
    }
    
    // Adicionar coluna nivel_trl se não existir
    $sql = "SHOW COLUMNS FROM desafios LIKE 'nivel_trl'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE desafios ADD COLUMN nivel_trl VARCHAR(20) AFTER descricao_pesquisa";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        echo "✓ Coluna 'nivel_trl' adicionada com sucesso!\n";
    } else {
        echo "✓ Coluna 'nivel_trl' já existe.\n";
    }
    
    echo "\nAtualização concluída com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?> 