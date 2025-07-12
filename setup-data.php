<?php
echo "<h1>⚙️ Configuração de Dados - ProTalent</h1>";

$host = 'localhost';
$username = 'root';
$password = 'AtivaFps123-';
$dbname = 'protalent';

echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔍 Verificando Dados Existentes</h2>";
    
    // Verificar usuário admin
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE email = 'admin@protalent.com'");
    $admin_exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    
    if ($admin_exists) {
        echo "✅ Usuário admin já existe<br>";
    } else {
        echo "❌ Usuário admin não encontrado - criando...<br>";
        
        // Criar usuário admin
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@protalent.com', $admin_password, 'admin']);
        echo "✅ Usuário admin criado com sucesso!<br>";
    }
    
    // Verificar talentos de exemplo
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM talents");
    $talents_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($talents_count > 0) {
        echo "✅ Talentos de exemplo já existem ($talents_count encontrados)<br>";
    } else {
        echo "❌ Talentos de exemplo não encontrados - criando...<br>";
        
        // Criar talentos de exemplo
        $talents = [
            ['João Silva', 'joao@email.com', '(11) 99999-9999', 'PHP, MySQL, JavaScript, Laravel', 5, 8000.00],
            ['Maria Santos', 'maria@email.com', '(11) 88888-8888', 'React, Node.js, TypeScript', 3, 7000.00],
            ['Pedro Costa', 'pedro@email.com', '(11) 77777-7777', 'Python, Django, PostgreSQL', 4, 7500.00]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO talents (name, email, phone, skills, experience_years, salary_expectation) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($talents as $talent) {
            $stmt->execute($talent);
        }
        
        echo "✅ Talentos de exemplo criados com sucesso!<br>";
    }
    
    // Verificar estatísticas finais
    echo "<h2>📊 Estatísticas Finais</h2>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "👥 Usuários: $user_count<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM talents");
    $talent_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "🎯 Talentos: $talent_count<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM talents WHERE status = 'available'");
    $available_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Disponíveis: $available_count<br>";
    
    // Testar autenticação
    echo "<h2>🔐 Testando Autenticação</h2>";
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute(['admin@protalent.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify('admin123', $user['password'])) {
        echo "✅ Autenticação funcionando!<br>";
        echo "👤 Nome: " . $user['name'] . "<br>";
        echo "📧 Email: " . $user['email'] . "<br>";
        echo "🔐 Função: " . $user['role'] . "<br>";
    } else {
        echo "❌ Problema na autenticação<br>";
    }
    
    // Resumo final
    echo "<h2>🎉 Configuração Concluída!</h2>";
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
    echo "<h3>✅ Sistema Pronto para Uso</h3>";
    echo "<p>O ProTalent foi configurado com sucesso!</p>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #bee5eb;'>";
    echo "<h4>🔑 Credenciais de Acesso</h4>";
    echo "<p><strong>Email:</strong> admin@protalent.com</p>";
    echo "<p><strong>Senha:</strong> admin123</p>";
    echo "</div>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='login.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
    echo "🚀 Acessar Sistema";
    echo "</a>";
    echo "<a href='index.php' style='background: #17a2b8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
    echo "📊 Dashboard";
    echo "</a>";
    echo "<a href='check.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
    echo "🔍 Verificar Sistema";
    echo "</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
    echo "<h3>❌ Erro na Configuração</h3>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    echo "</div>";
    
    echo "<h3>🔧 Soluções:</h3>";
    echo "<ul>";
    echo "<li>Verifique se o banco de dados existe</li>";
    echo "<li>Confirme as credenciais de acesso</li>";
    echo "<li>Execute o reset do banco se necessário</li>";
    echo "</ul>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='reset-db.php' style='background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
    echo "🔄 Reset Banco";
    echo "</a>";
    echo "<a href='diagnostico.php' style='background: #17a2b8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
    echo "🔍 Diagnóstico";
    echo "</a>";
    echo "</div>";
}

echo "</div>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
a:hover { opacity: 0.8; }
</style> 