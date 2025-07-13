<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id']) && !isset($_SESSION['empresa_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$user_data = null;
$user_type = '';

// Buscar dados do usuário baseado no tipo de sessão
if (isset($_SESSION['user_id'])) {
    // Usuário admin
    require_once 'models/User.php';
    $user = new User($db);
    $user->id = $_SESSION['user_id'];
    $user_data = $user->readOne();
    $user_type = 'admin';
} elseif (isset($_SESSION['empresa_id'])) {
    // Empresa
    require_once 'models/Empresa.php';
    $empresa = new Empresa($db);
    $empresa->id = $_SESSION['empresa_id'];
    $user_data = $empresa->readOne();
    $user_type = 'empresa';
}

include 'includes/header.php';
?>

<style>
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 0;
    margin-bottom: 20px;
}

.profile-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin: 0 auto 20px;
}

.profile-title {
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

.btn-profile {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.info-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid #667eea;
}

.info-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
}

.info-value {
    color: #333;
    font-size: 1.1rem;
}
</style>

<div class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Perfil do Usuário</h1>
                <p class="lead mb-0">
                    <?php echo $user_type == 'admin' ? 'Administrador' : 'Empresa'; ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo $user_type == 'admin' ? 'admin-dashboard.php' : 'empresa-dashboard.php'; ?>" 
                   class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="profile-card">
                <div class="profile-avatar">
                    <i class="fas fa-<?php echo $user_type == 'admin' ? 'user-shield' : 'building'; ?>"></i>
                </div>
                
                <h2 class="profile-title">
                    <?php echo $user_type == 'admin' ? 'Perfil do Administrador' : 'Perfil da Empresa'; ?>
                </h2>
                
                <?php if ($user_type == 'admin'): ?>
                    <!-- Perfil do Administrador -->
                    <div class="info-item">
                        <div class="info-label">Nome</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['name']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Função</div>
                        <div class="info-value">
                            <span class="badge bg-danger">Administrador</span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Data de Criação</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($user_data['created_at'])); ?></div>
                    </div>
                    
                <?php else: ?>
                    <!-- Perfil da Empresa -->
                    <div class="info-item">
                        <div class="info-label">Razão Social</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['razao_social']); ?></div>
                    </div>
                    
                    <?php if (!empty($user_data['nome_fantasia'])): ?>
                        <div class="info-item">
                            <div class="info-label">Nome Fantasia</div>
                            <div class="info-value"><?php echo htmlspecialchars($user_data['nome_fantasia']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <div class="info-label">CNPJ</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['cnpj']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Mentor</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['nome_mentor']); ?></div>
                    </div>
                    
                    <?php if (!empty($user_data['whatsapp'])): ?>
                        <div class="info-item">
                            <div class="info-label">WhatsApp</div>
                            <div class="info-value"><?php echo htmlspecialchars($user_data['whatsapp']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user_data['cidade'])): ?>
                        <div class="info-item">
                            <div class="info-label">Localização</div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($user_data['cidade']); ?>
                                <?php if (!empty($user_data['estado'])): ?>
                                    - <?php echo htmlspecialchars($user_data['estado']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <div class="info-label">Data de Cadastro</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($user_data['created_at'])); ?></div>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="<?php echo $user_type == 'admin' ? 'admin-dashboard.php' : 'empresa-dashboard.php'; ?>" 
                       class="btn btn-profile">
                        <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 