<?php
session_start();
require_once 'config/database.php';
require_once 'models/Empresa.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $empresa = new Empresa($db);
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($empresa->authenticate($email, $password)) {
        $_SESSION['empresa_id'] = $empresa->id;
        $_SESSION['empresa_nome'] = $empresa->nome_fantasia ?: $empresa->razao_social;
        $_SESSION['user_type'] = 'empresa';
        
        header("Location: empresa-dashboard.php");
        exit();
    } else {
        $error = 'Email ou senha incorretos!';
    }
}

include 'includes/header.php';
?>

<style>
.login-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.login-card {
    background: white;
    border-radius: 15px;
    padding: 40px;
    margin-top: -40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    z-index: 10;
}

.login-title {
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 8px;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-cadastrar {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 15px;
}

.btn-cadastrar:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.login-divider {
    text-align: center;
    margin: 30px 0;
    position: relative;
}

.login-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e9ecef;
}

.login-divider span {
    background: white;
    padding: 0 20px;
    color: #666;
    font-size: 0.9rem;
}
</style>

<div class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Login da Empresa</h1>
                <p class="lead">Acesse sua conta para gerenciar seus desafios</p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-card">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <h3 class="login-title">
                    <i class="fas fa-building me-2"></i>Entrar na Conta
                </h3>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="seu@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Sua senha" required>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </form>
                
                <div class="login-divider">
                    <span>ou</span>
                </div>
                
                <a href="cadastro-empresa.php" class="btn btn-cadastrar">
                    <i class="fas fa-plus me-2"></i>Cadastrar Nova Empresa
                </a>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="text-muted">
                        <i class="fas fa-arrow-left me-1"></i>Voltar ao início
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 