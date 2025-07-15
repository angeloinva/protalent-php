<?php
session_start();

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 1) {
        // Verificar requisitos
        $requirements_met = true;
        $errors = [];
        
        // Verificar PHP
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            $requirements_met = false;
            $errors[] = 'PHP 7.4 ou superior é necessário. Versão atual: ' . PHP_VERSION;
        }
        
        // Verificar extensões
        $required_extensions = ['pdo', 'pdo_mysql', 'session'];
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $requirements_met = false;
                $errors[] = "Extensão $ext não está instalada";
            }
        }
        
        if ($requirements_met) {
            $step = 2;
        } else {
            $error = implode('<br>', $errors);
        }
    } elseif ($step == 2) {
        // Configurar banco de dados
        $host = $_POST['host'] ?? 'localhost';
        $dbname = $_POST['dbname'] ?? 'protalentappbr_protalent';
        $username = $_POST['username'] ?? 'protalentappbr_root';
        $password = $_POST['password'] ?? 'AtivaFps123-';
        
        try {
            // Teste de conexão inicial
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Criar banco se não existir
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Conectar ao banco específico
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Executar script SQL
            if (!file_exists('database/schema.sql')) {
                throw new Exception('Arquivo database/schema.sql não encontrado');
            }
            
            $sql = file_get_contents('database/schema.sql');
            // Remover a linha USE database pois já estamos conectados
            $sql = preg_replace('/USE.*?;/', '', $sql);
            
            // Executar cada comando SQL separadamente
            $commands = explode(';', $sql);
            foreach ($commands as $command) {
                $command = trim($command);
                if (!empty($command)) {
                    try {
                        $pdo->exec($command);
                    } catch (PDOException $e) {
                        // Se for erro de duplicação, ignorar (dados já existem)
                        if ($e->getCode() == 23000) {
                            // Dados já existem, continuar
                            continue;
                        } else {
                            throw $e; // Re-throw outros erros
                        }
                    }
                }
            }
            
            // Atualizar arquivo de configuração
            $config_content = "<?php
class Database {
    private \$host = '$host';
    private \$db_name = '$dbname';
    private \$username = '$username';
    private \$password = '$password';
    private \$conn;

    public function getConnection() {
        \$this->conn = null;

        try {
            \$this->conn = new PDO(
                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name,
                \$this->username,
                \$this->password
            );
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            \$this->conn->exec(\"set names utf8\");
        } catch(PDOException \$exception) {
            echo \"Erro de conexão: \" . \$exception->getMessage();
        }

        return \$this->conn;
    }
}
?>";
            
            file_put_contents('config/database.php', $config_content);
            
            $success = 'Banco de dados configurado com sucesso!';
            $step = 3;
            
        } catch (Exception $e) {
            $error = 'Erro ao configurar banco: ' . $e->getMessage();
            // Debug adicional
            error_log("Erro de instalação: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - ProTalent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h3><i class="fas fa-cog me-2"></i>Instalação do ProTalent</h3>
                    </div>
                    <div class="card-body">
                        <!-- Progress Bar -->
                        <div class="progress mb-4">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?php echo ($step / 3) * 100; ?>%" 
                                 aria-valuenow="<?php echo $step; ?>" aria-valuemin="0" aria-valuemax="3">
                                Passo <?php echo $step; ?> de 3
                            </div>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                                <br><br>
                                <a href="diagnostico.php" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-bug me-2"></i>Executar Diagnóstico
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <?php if ($step == 1): ?>
                            <!-- Passo 1: Verificar Requisitos -->
                            <h4>Passo 1: Verificar Requisitos do Sistema</h4>
                            <div class="mb-3">
                                <strong>Versão do PHP:</strong> <?php echo PHP_VERSION; ?>
                                <?php if (version_compare(PHP_VERSION, '7.4.0', '>=')): ?>
                                    <span class="text-success">✅ OK</span>
                                <?php else: ?>
                                    <span class="text-danger">❌ Versão muito antiga</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Extensões PHP:</strong>
                                <ul class="list-unstyled ms-3">
                                    <?php
                                    $required_extensions = ['pdo', 'pdo_mysql', 'session'];
                                    foreach ($required_extensions as $ext):
                                    ?>
                                    <li>
                                        <?php echo $ext; ?>: 
                                        <?php if (extension_loaded($ext)): ?>
                                            <span class="text-success">✅ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">❌ Faltando</span>
                                        <?php endif; ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <form method="POST">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-2"></i>Continuar
                                </button>
                            </form>

                        <?php elseif ($step == 2): ?>
                            <!-- Passo 2: Configurar Banco -->
                            <h4>Passo 2: Configurar Banco de Dados</h4>
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="host" class="form-label">Host</label>
                                        <input type="text" class="form-control" id="host" name="host" 
                                               value="<?php echo $_POST['host'] ?? 'localhost'; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dbname" class="form-label">Nome do Banco</label>
                                        <input type="text" class="form-control" id="dbname" name="dbname" 
                                               value="<?php echo $_POST['dbname'] ?? 'protalentappbr_protalent'; ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Usuário</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo $_POST['username'] ?? 'protalentappbr_root'; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Senha</label>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               value="<?php echo $_POST['password'] ?? 'AtivaFps123-'; ?>">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="?step=1" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Voltar
                                    </a>
                                    <div>
                                        <a href="test-db.php" class="btn btn-outline-info me-2" target="_blank">
                                            <i class="fas fa-vial me-2"></i>Testar Conexão
                                        </a>
                                        <a href="reset-db.php" class="btn btn-outline-warning me-2" target="_blank">
                                            <i class="fas fa-refresh me-2"></i>Reset Banco
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-database me-2"></i>Configurar Banco
                                        </button>
                                    </div>
                                </div>
                            </form>

                        <?php elseif ($step == 3): ?>
                            <!-- Passo 3: Finalização -->
                            <h4>Passo 3: Instalação Concluída!</h4>
                            <div class="text-center">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                <h5 class="mt-3">Sistema instalado com sucesso!</h5>
                                <p class="text-muted">O ProTalent está pronto para uso.</p>
                                
                                <div class="alert alert-info">
                                    <strong>Credenciais padrão:</strong><br>
                                    Email: admin@protalent.com<br>
                                    Senha: admin123
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="login.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Acessar o Sistema
                                    </a>
                                    <a href="test.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-vial me-2"></i>Executar Testes
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 