<?php
echo "<h1>🧪 Teste de Conectividade com Banco de Dados</h1>";

// Configurações para teste
$host = 'localhost';
$username = 'protalentappbr_root';
$password = 'AtivaFps123-';
$dbname = 'protalentappbr_protalent';

echo "<h2>1. Testando conexão básica com MySQL</h2>";

try {
    // Teste 1: Conexão sem especificar banco
    echo "Tentando conectar ao MySQL...<br>";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão com MySQL estabelecida com sucesso!<br>";
    
    // Teste 2: Verificar se o banco existe
    echo "<h2>2. Verificando banco de dados</h2>";
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Banco '$dbname' já existe<br>";
    } else {
        echo "❌ Banco '$dbname' não existe - será criado<br>";
    }
    
    // Teste 3: Criar banco se não existir
    echo "<h2>3. Criando banco de dados</h2>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco '$dbname' criado/verificado com sucesso!<br>";
    
    // Teste 4: Conectar ao banco específico
    echo "<h2>4. Conectando ao banco específico</h2>";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão com banco '$dbname' estabelecida!<br>";
    
    // Teste 5: Verificar tabelas
    echo "<h2>5. Verificando tabelas</h2>";
    $tables = ['users', 'talents'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela '$table' existe<br>";
        } else {
            echo "❌ Tabela '$table' não existe<br>";
        }
    }
    
    // Teste 6: Executar script SQL
    echo "<h2>6. Executando script SQL</h2>";
    if (file_exists('database/schema.sql')) {
        $sql = file_get_contents('database/schema.sql');
        // Remover a linha USE database pois já estamos conectados
        $sql = preg_replace('/USE.*?;/', '', $sql);
        
        // Executar cada comando SQL separadamente para melhor controle de erros
        $commands = explode(';', $sql);
        $success_count = 0;
        $ignored_count = 0;
        
        foreach ($commands as $command) {
            $command = trim($command);
            if (!empty($command)) {
                try {
                    $pdo->exec($command);
                    $success_count++;
                } catch (PDOException $e) {
                    // Se for erro de duplicação, ignorar (dados já existem)
                    if ($e->getCode() == 23000) {
                        $ignored_count++;
                        // Não mostrar mensagem para cada comando ignorado
                    } else {
                        throw $e; // Re-throw outros erros
                    }
                }
            }
        }
        
        if ($ignored_count > 0) {
            echo "✅ Script SQL executado com sucesso! ($success_count comandos executados, $ignored_count ignorados por já existirem)<br>";
        } else {
            echo "✅ Script SQL executado com sucesso! ($success_count comandos executados)<br>";
        }
    } else {
        echo "❌ Arquivo database/schema.sql não encontrado<br>";
    }
    
    // Teste 7: Verificar dados
    echo "<h2>7. Verificando dados</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Usuários: $user_count<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM talents");
    $talent_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Talentos: $talent_count<br>";
    
    echo "<h2>🎉 Teste Concluído!</h2>";
    echo "<p>Se todos os testes acima mostraram ✅, o banco está funcionando corretamente!</p>";
    echo "<p><a href='install.php'>Voltar ao instalador</a> | <a href='login.php'>Acessar o sistema</a></p>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border: 1px solid red;'>";
    echo "<h3>❌ Erro de Conexão</h3>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    echo "</div>";
    
    echo "<h3>🔧 Possíveis Soluções:</h3>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Confirme se o usuário e senha estão corretos</li>";
    echo "<li>Verifique se o usuário tem permissão para criar bancos</li>";
    echo "<li>Teste a conexão manualmente no MySQL</li>";
    echo "</ul>";
    
    echo "<h3>📝 Comandos para testar manualmente:</h3>";
    echo "<pre>";
    echo "mysql -u $username -p\n";
    echo "CREATE DATABASE IF NOT EXISTS $dbname;\n";
    echo "USE $dbname;\n";
    echo "SHOW TABLES;\n";
    echo "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style> 