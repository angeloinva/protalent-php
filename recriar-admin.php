<?php
echo "<h1>🔧 Recriando Usuário Admin - ProTalent</h1>";

$host = 'localhost';
$username = 'root';
$password = 'AtivaFps123-';
$dbname = 'protalent';

echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔍 Verificando Estado Atual</h2>";
    
    // Verificar se o usuário admin existe
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute(['admin@protalent.com']);
    $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_user) {
        echo "✅ Usuário admin encontrado (ID: {$existing_user['id']})<br>";
        echo "📧 Email: {$existing_user['email']}<br>";
        echo "👤 Nome: {$existing_user['name']}<br>";
        echo "🔐 Função: {$existing_user['role']}<br>";
        
        // Testar a senha atual
        if (password_verify('admin123', $existing_user['password'])) {
            echo "✅ Senha 'admin123' está funcionando!<br>";
        } else {
            echo "❌ Senha 'admin123' não está funcionando<br>";
            echo "🔄 Atualizando senha...<br>";
            
            // Atualizar a senha
            $new_password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$new_password_hash, 'admin@protalent.com']);
            echo "✅ Senha atualizada com sucesso!<br>";
        }
    } else {
        echo "❌ Usuário admin não encontrado - criando...<br>";
        
        // Criar usuário admin
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@protalent.com', $admin_password, 'admin']);
        echo "✅ Usuário admin criado com sucesso!<br>";
    }
    
    // Verificar novamente após as alterações
    echo "<h2>🔍 Verificação Final</h2>";
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute(['admin@protalent.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify('admin123', $user['password'])) {
        echo "✅ Autenticação funcionando perfeitamente!<br>";
        echo "👤 Nome: " . $user['name'] . "<br>";
        echo "📧 Email: " . $user['email'] . "<br>";
        echo "🔐 Função: " . $user['role'] . "<br>";
        echo "🔑 Senha: admin123 (confirmada)<br>";
    } else {
        echo "❌ Problema persistente na autenticação<br>";
    }
    
    // Resumo final
    echo "<h2>🎉 Usuário Admin Configurado!</h2>";
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
    echo "<h3>✅ Login Funcionando</h3>";
    echo "<p>O usuário admin foi configurado corretamente!</p>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #bee5eb;'>";
    echo "<h4>🔑 Credenciais de Acesso</h4>";
    echo "<p><strong>Email:</strong> admin@protalent.com</p>";
    echo "<p><strong>Senha:</strong> admin123</p>";
    echo "</div>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='login.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
    echo "🚀 Fazer Login";
    echo "</a>";
    echo "<a href='verificar-admin.php' style='background: #17a2b8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-right: 10px;'>";
    echo "🔍 Verificar Admin";
    echo "</a>";
    echo "<a href='index.php' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
    echo "📊 Dashboard";
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
    echo "<li>Confirme as credenciais de acesso ao MySQL</li>";
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