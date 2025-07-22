<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Empresa.php'; // Added this line for Empresa model
require_once 'models/Professor.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $empresa = new Empresa($db);
    $professor = new Professor($db);

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($user->authenticate($email, $password)) {
        // Login como usuário comum
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->nome;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->tipo;
        // Redireciona para dashboard de usuário, admin ou para redirect
        if ($user->tipo === 'admin') {
            header("Location: admin-dashboard.php");
            exit();
        } elseif ($redirect) {
            header("Location: $redirect");
        } else {
            header("Location: index.php");
        }
        exit();
    } elseif ($empresa->authenticate($email, $password)) {
        // Login como empresa
        $_SESSION['empresa_id'] = $empresa->id;
        $_SESSION['empresa_nome'] = $empresa->nome_fantasia ?: $empresa->razao_social;
        $_SESSION['user_type'] = 'empresa';
        // Redireciona para dashboard de empresa ou para redirect
        if ($redirect) {
            header("Location: $redirect");
        } else {
            header("Location: empresa-dashboard.php");
        }
        exit();
    } elseif ($professor->authenticate($email, $password)) {
        // Login como professor
        $_SESSION['professor_id'] = $professor->id;
        $_SESSION['professor_nome'] = $professor->nome;
        $_SESSION['user_type'] = 'professor';
        if ($redirect) {
            header("Location: $redirect");
        } else {
            header("Location: professor-dashboard.php");
        }
        exit();
    } else {
        $error = 'Email ou senha incorretos!';
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-sign-in-alt me-2"></i>Login</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <a href="cadastro-professor.php<?= $redirect ? '?redirect=' . urlencode($redirect) : '' ?>" class="btn btn-link">Cadastrar-se como professor</a>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <strong>Credenciais padrão:</strong><br>
                        Email: admin@protalent.com<br>
                        Senha: admin123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 