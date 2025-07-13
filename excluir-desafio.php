<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id']) && !isset($_SESSION['empresa_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';
require_once 'models/Desafio.php';

$database = new Database();
$db = $database->getConnection();
$desafio = new Desafio($db);

$error = '';
$success = '';
$desafio_data = null;

// Verificar se ID do desafio foi fornecido
if (!isset($_GET['id'])) {
    header("Location: " . (isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'));
    exit();
}

$desafio->id = $_GET['id'];
$desafio_data = $desafio->readOne();

if (!$desafio_data) {
    header("Location: " . (isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'));
    exit();
}

// Verificar permissões
$is_admin = isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'admin';
$is_empresa_owner = isset($_SESSION['empresa_id']) && $desafio_data['empresa_id'] == $_SESSION['empresa_id'];

if (!$is_admin && !$is_empresa_owner) {
    header("Location: " . (isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'));
    exit();
}

// Processar exclusão
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_exclusao'])) {
    if ($desafio->delete()) {
        $success = 'Desafio excluído com sucesso!';
        // Redirecionar após 2 segundos
        header("refresh:2;url=" . (isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'));
    } else {
        $error = 'Erro ao excluir desafio!';
    }
}

include 'includes/header.php';
?>

<style>
.exclusao-section {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 40px 0;
    text-align: center;
}

.confirmacao-section {
    padding: 40px 0;
}

.confirmacao-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    text-align: center;
}

.confirmacao-icon {
    font-size: 4rem;
    color: #e74c3c;
    margin-bottom: 20px;
}

.confirmacao-title {
    color: #333;
    margin-bottom: 20px;
}

.confirmacao-message {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 30px;
}

.desafio-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    border-left: 4px solid #e74c3c;
}

.desafio-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.desafio-description {
    color: #666;
    line-height: 1.5;
}

.btn-excluir {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    border: none;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    margin: 10px;
}

.btn-excluir:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    color: white;
}

.btn-cancelar {
    background: #6c757d;
    border: none;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    margin: 10px;
}

.btn-cancelar:hover {
    background: #5a6268;
    transform: translateY(-2px);
    color: white;
}
</style>

<div class="exclusao-section">
    <div class="container">
        <h1 class="display-4 fw-bold">
            <i class="fas fa-trash me-3"></i>Excluir Desafio
        </h1>
        <p class="lead">Confirme a exclusão do desafio</p>
    </div>
</div>

<div class="confirmacao-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmacao-card">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <div class="confirmacao-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 class="confirmacao-title">Desafio Excluído!</h2>
                        <p class="confirmacao-message">
                            O desafio foi excluído com sucesso. Você será redirecionado em alguns segundos...
                        </p>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php else: ?>
                        <div class="confirmacao-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h2 class="confirmacao-title">Confirmar Exclusão</h2>
                        <p class="confirmacao-message">
                            Você está prestes a excluir permanentemente este desafio. Esta ação não pode ser desfeita.
                        </p>
                        
                        <div class="desafio-info">
                            <div class="desafio-title">
                                <?php echo htmlspecialchars($desafio_data['titulo']); ?>
                            </div>
                            <div class="desafio-description">
                                <?php echo htmlspecialchars(substr($desafio_data['descricao_problema'], 0, 200)) . (strlen($desafio_data['descricao_problema']) > 200 ? '...' : ''); ?>
                            </div>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="confirmar_exclusao" value="1">
                            <button type="submit" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este desafio? Esta ação não pode ser desfeita.')">
                                <i class="fas fa-trash me-2"></i>Confirmar Exclusão
                            </button>
                            <a href="<?php echo isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'; ?>" 
                               class="btn btn-cancelar">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 