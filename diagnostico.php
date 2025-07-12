<?php
echo "<h1>🔍 Diagnóstico Completo - ProTalent</h1>";

// Função para verificar extensão
function checkExtension($ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "✅" : "❌";
    echo "$status $ext: " . ($loaded ? "Carregada" : "Não carregada") . "<br>";
    return $loaded;
}

// Função para verificar arquivo
function checkFile($file) {
    $exists = file_exists($file);
    $status = $exists ? "✅" : "❌";
    echo "$status $file: " . ($exists ? "Existe" : "Não encontrado") . "<br>";
    return $exists;
}

// Função para verificar permissão
function checkPermission($path) {
    $writable = is_writable($path);
    $status = $writable ? "✅" : "❌";
    echo "$status $path: " . ($writable ? "Gravável" : "Sem permissão") . "<br>";
    return $writable;
}

?>

<div style="font-family: Arial, sans-serif; margin: 20px;">

<h2>1. Informações do Sistema</h2>
<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <strong>Sistema Operacional:</strong> <?php echo PHP_OS; ?><br>
    <strong>Versão do PHP:</strong> <?php echo PHP_VERSION; ?><br>
    <strong>Servidor Web:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido'; ?><br>
    <strong>Diretório Atual:</strong> <?php echo getcwd(); ?><br>
    <strong>Usuário do Servidor:</strong> <?php echo get_current_user(); ?><br>
</div>

<h2>2. Extensões PHP Necessárias</h2>
<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <?php
    $required_extensions = ['pdo', 'pdo_mysql', 'session', 'openssl'];
    $extensions_ok = true;
    foreach ($required_extensions as $ext) {
        if (!checkExtension($ext)) {
            $extensions_ok = false;
        }
    }
    ?>
</div>

<h2>3. Arquivos do Sistema</h2>
<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <?php
    $required_files = [
        'config/database.php',
        'models/User.php',
        'models/Talent.php',
        'includes/header.php',
        'includes/footer.php',
        'database/schema.sql',
        'index.php',
        'login.php',
        'talents.php',
        'users.php'
    ];
    
    $files_ok = true;
    foreach ($required_files as $file) {
        if (!checkFile($file)) {
            $files_ok = false;
        }
    }
    ?>
</div>

<h2>4. Permissões de Diretórios</h2>
<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <?php
    $writable_dirs = ['.', 'config'];
    $permissions_ok = true;
    foreach ($writable_dirs as $dir) {
        if (!checkPermission($dir)) {
            $permissions_ok = false;
        }
    }
    ?>
</div>

<h2>5. Teste de Conexão com MySQL</h2>
<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <?php
    $host = 'localhost';
    $username = 'root';
    $password = 'AtivaFps123-';
    
    try {
        echo "Tentando conectar ao MySQL...<br>";
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Conexão com MySQL estabelecida!<br>";
        
        // Verificar versão do MySQL
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "✅ Versão do MySQL: $version<br>";
        
        // Verificar bancos existentes
        echo "📋 Bancos de dados existentes:<br>";
        $databases = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($databases as $db) {
            echo "&nbsp;&nbsp;• $db<br>";
        }
        
    } catch (PDOException $e) {
        echo "❌ Erro de conexão: " . $e->getMessage() . "<br>";
        echo "Código: " . $e->getCode() . "<br>";
    }
    ?>
</div>

<h2>6. Configuração Atual do Banco</h2>
<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <?php
    if (file_exists('config/database.php')) {
        echo "✅ Arquivo de configuração existe<br>";
        $config_content = file_get_contents('config/database.php');
        if (strpos($config_content, 'AtivaFps123-') !== false) {
            echo "✅ Senha configurada corretamente<br>";
        } else {
            echo "⚠️ Senha pode estar diferente da esperada<br>";
        }
    } else {
        echo "❌ Arquivo de configuração não existe<br>";
    }
    ?>
</div>

<h2>7. Resumo do Diagnóstico</h2>
<div style="background: <?php echo ($extensions_ok && $files_ok && $permissions_ok) ? '#d4edda' : '#f8d7da'; ?>; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid <?php echo ($extensions_ok && $files_ok && $permissions_ok) ? '#c3e6cb' : '#f5c6cb'; ?>;">
    <?php if ($extensions_ok && $files_ok && $permissions_ok): ?>
        <h3 style="color: #155724;">🎉 Sistema Pronto!</h3>
        <p>Todos os requisitos estão atendidos. O sistema deve funcionar corretamente.</p>
        <a href="install.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Ir para Instalador</a>
    <?php else: ?>
        <h3 style="color: #721c24;">⚠️ Problemas Detectados</h3>
        <p>Alguns requisitos não estão atendidos. Verifique os itens marcados com ❌ acima.</p>
        <ul>
            <?php if (!$extensions_ok): ?>
                <li>Instale as extensões PHP necessárias</li>
            <?php endif; ?>
            <?php if (!$files_ok): ?>
                <li>Verifique se todos os arquivos estão presentes</li>
            <?php endif; ?>
            <?php if (!$permissions_ok): ?>
                <li>Ajuste as permissões dos diretórios</li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>

<h2>8. Comandos Úteis</h2>
<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <h4>Para Windows (XAMPP/WAMP):</h4>
    <pre style="background: #e9ecef; padding: 10px; border-radius: 3px;">
# Iniciar MySQL
# XAMPP: C:\xampp\mysql\bin\mysql.exe -u root -p
# WAMP: C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe -u root -p

# Criar banco manualmente
CREATE DATABASE protalent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE protalent;
SOURCE database/schema.sql;
    </pre>
    
    <h4>Para Linux/Mac:</h4>
    <pre style="background: #e9ecef; padding: 10px; border-radius: 3px;">
# Instalar PHP e MySQL
sudo apt-get install php mysql-server php-mysql

# Criar banco manualmente
mysql -u root -p
CREATE DATABASE protalent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE protalent;
SOURCE database/schema.sql;
    </pre>
</div>

</div>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
pre { background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6; }
</style> 