<?php
session_start();

// Verificar se é administrador
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';
require_once 'models/Professor.php';

$database = new Database();
$db = $database->getConnection();
$professor = new Professor($db);

// Buscar apenas professores interessados em edições futuras
$query = "SELECT * FROM professores WHERE interessado_edicoes_futuras = 1 ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$professores_interessados = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<style>
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 0;
    margin-bottom: 20px;
}

.professor-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    border-left: 4px solid #667eea;
}

.professor-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.professor-name {
    color: #333;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 1.2rem;
}

.professor-email {
    color: #667eea;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.professor-whatsapp {
    color: #25d366;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.professor-date {
    color: #999;
    font-size: 0.8rem;
    margin-bottom: 10px;
}

.professor-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-action {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-1px);
}

.stats-summary {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-align: center;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 5px;
}

.stats-label {
    color: #666;
    font-size: 1rem;
}
</style>

<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Professores Interessados</h1>
                <p class="lead mb-0">Lista de professores interessados em participar das próximas edições</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="admin-dashboard.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
                <a href="logout.php" class="btn btn-outline-light" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Resumo -->
    <div class="stats-summary">
        <div class="stats-number"><?php echo count($professores_interessados); ?></div>
        <div class="stats-label">Professores Interessados</div>
    </div>

    <!-- Lista de Professores -->
    <?php if (empty($professores_interessados)): ?>
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Nenhum professor interessado ainda</h4>
            <p class="text-muted">Os professores aparecerão aqui quando se cadastrarem para interesse em edições futuras.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($professores_interessados as $prof): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="professor-card">
                        <div class="professor-name">
                            <i class="fas fa-chalkboard-teacher me-2"></i>
                            <?php echo htmlspecialchars($prof['nome']); ?>
                        </div>
                        
                        <div class="professor-email">
                            <i class="fas fa-envelope me-2"></i>
                            <?php echo htmlspecialchars($prof['email']); ?>
                        </div>
                        
                        <?php if (!empty($prof['whatsapp'])): ?>
                            <div class="professor-whatsapp">
                                <i class="fab fa-whatsapp me-2"></i>
                                <?php echo htmlspecialchars($prof['whatsapp']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($prof['instituicao'])): ?>
                            <div class="professor-info">
                                <i class="fas fa-university me-2"></i>
                                <?php echo htmlspecialchars($prof['instituicao']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($prof['area_atuacao'])): ?>
                            <div class="professor-info">
                                <i class="fas fa-graduation-cap me-2"></i>
                                <?php echo htmlspecialchars($prof['area_atuacao']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="professor-date">
                            <i class="fas fa-calendar me-2"></i>
                            Interesse registrado em: <?php echo date('d/m/Y H:i', strtotime($prof['created_at'])); ?>
                        </div>
                        
                        <div class="professor-actions">
                            <?php if (!empty($prof['whatsapp'])): ?>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $prof['whatsapp']); ?>" 
                                   target="_blank" class="btn btn-success btn-action">
                                    <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                </a>
                            <?php endif; ?>
                            
                            <a href="mailto:<?php echo htmlspecialchars($prof['email']); ?>" 
                               class="btn btn-primary btn-action">
                                <i class="fas fa-envelope me-1"></i>Email
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 