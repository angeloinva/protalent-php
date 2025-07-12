<?php
echo "<h1>📊 Status do Sistema ProTalent</h1>";

$host = 'localhost';
$username = 'root';
$password = 'AtivaFps123-';
$dbname = 'protalent';

echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";

// Verificar conexão
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
    echo "<h3>✅ Sistema Funcionando</h3>";
    echo "<p>Conexão com banco de dados estabelecida com sucesso!</p>";
    echo "</div>";
    
    // Estatísticas
    echo "<h2>📈 Estatísticas do Sistema</h2>";
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 15px 0;'>";
    
    // Usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; border-left: 4px solid #2196f3;'>";
    echo "<h4>👥 Usuários</h4>";
    echo "<p style='font-size: 24px; margin: 0;'><strong>$user_count</strong></p>";
    echo "</div>";
    
    // Talentos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM talents");
    $talent_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; border-left: 4px solid #4caf50;'>";
    echo "<h4>🎯 Talentos</h4>";
    echo "<p style='font-size: 24px; margin: 0;'><strong>$talent_count</strong></p>";
    echo "</div>";
    
    // Talentos disponíveis
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM talents WHERE status = 'available'");
    $available_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; border-left: 4px solid #ff9800;'>";
    echo "<h4>✅ Disponíveis</h4>";
    echo "<p style='font-size: 24px; margin: 0;'><strong>$available_count</strong></p>";
    echo "</div>";
    
    // Talentos contratados
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM talents WHERE status = 'hired'");
    $hired_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<div style='background: #fce4ec; padding: 15px; border-radius: 5px; border-left: 4px solid #e91e63;'>";
    echo "<h4>💼 Contratados</h4>";
    echo "<p style='font-size: 24px; margin: 0;'><strong>$hired_count</strong></p>";
    echo "</div>";
    
    echo "</div>";
    
    // Verificar usuário admin
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE email = 'admin@protalent.com'");
    $admin_exists = $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    
    if ($admin_exists) {
        echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #bee5eb;'>";
        echo "<h4>🔐 Usuário Admin</h4>";
        echo "<p><strong>Email:</strong> admin@protalent.com</p>";
        echo "<p><strong>Senha:</strong> admin123</p>";
        echo "<p><strong>Status:</strong> ✅ Disponível</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
        echo "<h4>⚠️ Usuário Admin</h4>";
        echo "<p>Usuário admin não encontrado. Execute o reset do banco.</p>";
        echo "</div>";
    }
    
    // Ações rápidas
    echo "<h2>🚀 Ações Rápidas</h2>";
    echo "<div style='display: flex; gap: 10px; flex-wrap: wrap; margin: 15px 0;'>";
    echo "<a href='login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
    echo "🔑 Acessar Sistema";
    echo "</a>";
    echo "<a href='test-db.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
    echo "🧪 Testar Conexão";
    echo "</a>";
    echo "<a href='reset-db.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
    echo "🔄 Reset Banco";
    echo "</a>";
    echo "<a href='diagnostico.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
    echo "🔍 Diagnóstico";
    echo "</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
    echo "<h3>❌ Problema Detectado</h3>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    echo "</div>";
    
    echo "<h2>🔧 Soluções</h2>";
    echo "<div style='display: flex; gap: 10px; flex-wrap: wrap; margin: 15px 0;'>";
    echo "<a href='diagnostico.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
    echo "🔍 Executar Diagnóstico";
    echo "</a>";
    echo "<a href='test-db.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
    echo "🧪 Testar Conexão";
    echo "</a>";
    echo "<a href='install.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
    echo "⚙️ Instalador";
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