<?php
echo "<h1>🧪 Teste do Sistema ProTalent</h1>";

// Teste 1: Verificar versão do PHP
echo "<h2>1. Verificação do PHP</h2>";
echo "Versão do PHP: " . phpversion() . "<br>";
echo "Extensões necessárias:<br>";
$required_extensions = ['pdo', 'pdo_mysql', 'session'];
foreach ($required_extensions as $ext) {
    echo "- $ext: " . (extension_loaded($ext) ? "✅ OK" : "❌ FALTANDO") . "<br>";
}

// Teste 2: Verificar conectividade com banco
echo "<h2>2. Teste de Conectividade com Banco</h2>";
try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ Conexão com banco estabelecida com sucesso!<br>";
        
        // Teste 3: Verificar se as tabelas existem
        echo "<h2>3. Verificação das Tabelas</h2>";
        $tables = ['users', 'talents'];
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Tabela '$table' existe<br>";
            } else {
                echo "❌ Tabela '$table' não encontrada<br>";
            }
        }
        
        // Teste 4: Verificar dados de exemplo
        echo "<h2>4. Verificação de Dados</h2>";
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Usuários cadastrados: $user_count<br>";
        
        $stmt = $db->query("SELECT COUNT(*) as count FROM talents");
        $talent_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Talentos cadastrados: $talent_count<br>";
        
        // Teste 5: Testar autenticação
        echo "<h2>5. Teste de Autenticação</h2>";
        require_once 'models/User.php';
        $user = new User($db);
        
        if ($user->authenticate('admin@protalent.com', 'admin123')) {
            echo "✅ Autenticação do admin funcionando<br>";
        } else {
            echo "❌ Problema na autenticação do admin<br>";
        }
        
    } else {
        echo "❌ Falha na conexão com banco<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

// Teste 6: Verificar arquivos do sistema
echo "<h2>6. Verificação de Arquivos</h2>";
$required_files = [
    'config/database.php',
    'models/User.php',
    'models/Talent.php',
    'includes/header.php',
    'includes/footer.php',
    'index.php',
    'login.php',
    'talents.php',
    'users.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file não encontrado<br>";
    }
}

// Teste 7: Verificar permissões
echo "<h2>7. Verificação de Permissões</h2>";
$writable_dirs = ['.'];
foreach ($writable_dirs as $dir) {
    if (is_writable($dir)) {
        echo "✅ Diretório '$dir' tem permissão de escrita<br>";
    } else {
        echo "❌ Diretório '$dir' sem permissão de escrita<br>";
    }
}

echo "<h2>🎉 Resumo do Teste</h2>";
echo "<p>Se todos os testes acima mostraram ✅, o sistema está pronto para uso!</p>";
echo "<p><a href='login.php'>Clique aqui para acessar o sistema</a></p>";
?> 