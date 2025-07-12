<?php
require_once 'config/database.php';

echo "<h2>Verificação do Usuário Admin</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p style='color: green;'>✓ Conexão com banco de dados estabelecida</p>";
        
        // Verificar se a tabela users existe
        $stmt = $db->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Tabela 'users' existe</p>";
            
            // Verificar se o usuário admin existe
            $stmt = $db->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
            $stmt->execute(['admin@protalent.com']);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<p style='color: green;'>✓ Usuário admin encontrado</p>";
                echo "<p><strong>ID:</strong> " . $user['id'] . "</p>";
                echo "<p><strong>Email:</strong> " . $user['email'] . "</p>";
                echo "<p><strong>Role:</strong> " . $user['role'] . "</p>";
                echo "<p><strong>Hash da senha:</strong> " . substr($user['password'], 0, 20) . "...</p>";
                
                // Testar o login
                $senha_teste = 'admin123';
                if (password_verify($senha_teste, $user['password'])) {
                    echo "<p style='color: green;'>✓ Senha 'admin123' está correta!</p>";
                } else {
                    echo "<p style='color: red;'>✗ Senha 'admin123' está incorreta!</p>";
                    
                    // Verificar qual é a senha correta (para debug)
                    echo "<h3>Testando outras senhas comuns:</h3>";
                    $senhas_teste = ['admin', '123456', 'password', 'admin123', 'protalent'];
                    foreach ($senhas_teste as $senha) {
                        if (password_verify($senha, $user['password'])) {
                            echo "<p style='color: green;'>✓ Senha correta encontrada: '$senha'</p>";
                            break;
                        }
                    }
                }
            } else {
                echo "<p style='color: red;'>✗ Usuário admin não encontrado!</p>";
                
                // Listar todos os usuários
                $stmt = $db->query("SELECT id, email, role FROM users");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<h3>Usuários existentes no banco:</h3>";
                if (count($users) > 0) {
                    echo "<ul>";
                    foreach ($users as $u) {
                        echo "<li>ID: {$u['id']} - Email: {$u['email']} - Role: {$u['role']}</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>Nenhum usuário encontrado no banco.</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>✗ Tabela 'users' não existe!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Erro ao conectar com o banco de dados</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Ações recomendadas:</h3>";
echo "<p>1. <a href='setup-data.php'>Executar setup-data.php</a> - Para recriar o usuário admin</p>";
echo "<p>2. <a href='reset-db.php'>Executar reset-db.php</a> - Para resetar completamente o banco</p>";
echo "<p>3. <a href='install.php'>Executar install.php</a> - Para reinstalar o sistema</p>";
?> 