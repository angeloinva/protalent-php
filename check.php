<?php
echo "<h1>🔍 Verificação Rápida - ProTalent</h1>";

$host = 'localhost';
$username = 'root';
$password = 'AtivaFps123-';
$dbname = 'protalent';

echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";

try {
    // Teste 1: Conexão com banco
    echo "<h2>1. Testando Conexão</h2>";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão com banco estabelecida!<br>";
    
    // Teste 2: Verificar tabelas
    echo "<h2>2. Verificando Tabelas</h2>";
    $tables = ['users', 'talents'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela '$table' existe<br>";
        } else {
            echo "❌ Tabela '$table' não existe<br>";
        }
    }
    
    // Teste 3: Verificar dados
    echo "<h2>3. Verificando Dados</h2>";
    
    // Usuários
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "👥 Usuários: $user_count<br>";
    
    // Talentos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM talents");
    $talent_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "🎯 Talentos: $talent_count<br>";
    
    // Teste 4: Verificar usuário admin
    echo "<h2>4. Verificando Usuário Admin</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE email = 'admin@protalent.com'");
    $admin_exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    
    if ($admin_exists) {
        echo "✅ Usuário admin existe<br>";
        echo "📧 Email: admin@protalent.com<br>";
        echo "🔑 Senha: admin123<br>";
    } else {
        echo "❌ Usuário admin não encontrado<br>";
    }
    
    // Teste 5: Testar autenticação
    echo "<h2>5. Testando Autenticação</h2>";
    if ($admin_exists) {
        $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->execute(['admin@protalent.com']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify('admin123', $user['password'])) {
            echo "✅ Autenticação funcionando!<br>";
            echo "👤 Nome: " . $user['name'] . "<br>";
            echo "🔐 Função: " . $user['role'] . "<br>";
        } else {
            echo "❌ Problema na autenticação<br>";
        }
    }
    
    // Resumo
    echo "<h2>🎉 Resumo</h2>";
    if ($user_count > 0 && $talent_count > 0 && $admin_exists) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
        echo "<h3>✅ Sistema Funcionando Perfeitamente!</h3>";
        echo "<p>O ProTalent está pronto para uso.</p>";
        echo "</div>";
        
        echo "<div style='margin-top: 20px;'>";
        echo "<a href='login.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
        echo "🚀 Acessar Sistema";
        echo "</a>";
        echo "<a href='index.php' style='background: #17a2b8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
        echo "📊 Dashboard";
        echo "</a>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
        echo "<h3>⚠️ Problemas Detectados</h3>";
        echo "<p>Alguns componentes não estão funcionando corretamente.</p>";
        echo "</div>";
        
        echo "<div style='margin-top: 20px;'>";
        echo "<a href='reset-db.php' style='background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
        echo "🔄 Reset Banco";
        echo "</a>";
        echo "<a href='diagnostico.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
        echo "🔍 Diagnóstico";
        echo "</a>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
    echo "<h3>❌ Erro de Conexão</h3>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    echo "</div>";
    
    echo "<h3>🔧 Soluções:</h3>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Confirme as credenciais de acesso</li>";
    echo "<li>Execute o diagnóstico completo</li>";
    echo "</ul>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='diagnostico.php' style='background: #17a2b8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
    echo "🔍 Diagnóstico Completo";
    echo "</a>";
    echo "<a href='install.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
    echo "⚙️ Instalador";
    echo "</a>";
    echo "</div>";
}

echo "</div>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
a:hover { opacity: 0.8; }
</style> 