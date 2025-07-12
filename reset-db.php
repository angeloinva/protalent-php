<?php
echo "<h1>🔄 Reset do Banco de Dados - ProTalent</h1>";

$host = 'localhost';
$username = 'root';
$password = 'AtivaFps123-';
$dbname = 'protalent';

echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    try {
        echo "<h2>🔄 Executando Reset...</h2>";
        
        // Conectar ao MySQL
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Dropar banco se existir
        echo "🗑️ Removendo banco de dados existente...<br>";
        $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
        
        // Criar banco novamente
        echo "🏗️ Criando banco de dados...<br>";
        $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Conectar ao banco específico
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Executar script SQL
        echo "📝 Executando script SQL...<br>";
        if (file_exists('database/schema.sql')) {
            $sql = file_get_contents('database/schema.sql');
            // Remover a linha USE database
            $sql = preg_replace('/USE.*?;/', '', $sql);
            
            $commands = explode(';', $sql);
            foreach ($commands as $command) {
                $command = trim($command);
                if (!empty($command)) {
                    $pdo->exec($command);
                }
            }
        }
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
        echo "<h3>✅ Reset Concluído com Sucesso!</h3>";
        echo "<p>O banco de dados foi resetado e recriado com os dados padrão.</p>";
        echo "</div>";
        
        echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #bee5eb;'>";
        echo "<strong>Credenciais padrão:</strong><br>";
        echo "Email: admin@protalent.com<br>";
        echo "Senha: admin123<br>";
        echo "</div>";
        
        echo "<div style='margin-top: 20px;'>";
        echo "<a href='test-db.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Testar Conexão</a>";
        echo "<a href='login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Acessar Sistema</a>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
        echo "<h3>❌ Erro no Reset</h3>";
        echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
        echo "</div>";
    }
} else {
    echo "<h2>⚠️ Aviso Importante</h2>";
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #ffeaa7;'>";
    echo "<p><strong>ATENÇÃO:</strong> Este processo irá:</p>";
    echo "<ul>";
    echo "<li>🗑️ <strong>EXCLUIR</strong> completamente o banco de dados atual</li>";
    echo "<li>🏗️ Criar um novo banco de dados limpo</li>";
    echo "<li>📝 Inserir os dados padrão do sistema</li>";
    echo "<li>⚠️ <strong>PERDER</strong> todos os dados existentes (usuários, talentos, etc.)</li>";
    echo "</ul>";
    echo "<p><strong>Esta ação não pode ser desfeita!</strong></p>";
    echo "</div>";
    
    echo "<form method='POST' style='margin-top: 20px;'>";
    echo "<input type='hidden' name='confirm' value='1'>";
    echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;' onclick='return confirm(\"Tem certeza que deseja resetar o banco? Todos os dados serão perdidos!\")'>";
    echo "🔄 Confirmar Reset do Banco";
    echo "</button>";
    echo "</form>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='test-db.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Testar Conexão</a>";
    echo "<a href='diagnostico.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Diagnóstico</a>";
    echo "</div>";
}

echo "</div>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
button:hover { opacity: 0.8; }
</style> 