<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$equipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$equipe_id) {
    header('Location: index.php');
    exit();
}
$stmtEquipe = $db->prepare('SELECT equipes.*, professores.nome AS professor_nome, professores.email AS professor_email, professores.whatsapp AS professor_whatsapp, professores.instituicao AS professor_instituicao FROM equipes JOIN professores ON equipes.professor_id = professores.id WHERE equipes.id = ?');
$stmtEquipe->execute([$equipe_id]);
$equipe = $stmtEquipe->fetch(PDO::FETCH_ASSOC);
if (!$equipe) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Equipe não encontrada.</div></div>';
    exit();
}
$stmtAlunos = $db->prepare('SELECT * FROM alunos WHERE equipe_id = ? ORDER BY nome');
$stmtAlunos->execute([$equipe_id]);
$alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
include 'includes/header.php';
?>

<style>
.equipe-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    margin-bottom: 30px;
}

.equipe-content {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.equipe-title {
    color: #333;
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.equipe-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    color: #666;
}

.meta-item i {
    margin-right: 8px;
    color: #667eea;
}

.equipe-section {
    margin-bottom: 30px;
}

.section-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 1.2rem;
    border-bottom: 2px solid #667eea;
    padding-bottom: 8px;
}

.aluno-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 4px solid #667eea;
    transition: transform 0.2s ease;
}

.aluno-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.aluno-info {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.aluno-info i {
    margin-right: 10px;
    color: #667eea;
    width: 20px;
}

.professor-card {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    border: 2px solid #667eea;
}

.professor-title {
    color: #1976d2;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.back-button {
    background: transparent;
    border: 2px solid white;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.back-button:hover {
    background: white;
    color: #667eea;
    text-decoration: none;
}

.stats-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 5px;
}

.stats-label {
    color: #666;
    font-size: 0.9rem;
}
</style>

<div class="equipe-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">
                    <i class="fas fa-users me-3"></i>Detalhes da Equipe
                </h1>
                <p class="lead mb-0">Informações completas sobre a equipe e seus membros</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="javascript:history.back()" class="back-button">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="equipe-content">
                <h2 class="equipe-title">
                    <i class="fas fa-users me-2"></i><?= htmlspecialchars($equipe['nome']) ?>
                </h2>
                
                <div class="equipe-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Criada em: <?= date('d/m/Y', strtotime($equipe['created_at'])) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Há <?= date_diff(date_create($equipe['created_at']), date_create())->days ?> dias</span>
                    </div>
                </div>
                
                <!-- Informações do Professor -->
                <div class="equipe-section">
                    <h3 class="section-title">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Professor Responsável
                    </h3>
                    <div class="professor-card">
                        <div class="professor-title">
                            <i class="fas fa-user-tie me-2"></i><?= htmlspecialchars($equipe['professor_nome']) ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="aluno-info">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= htmlspecialchars($equipe['professor_email']) ?></span>
                                </div>
                                <?php if ($equipe['professor_whatsapp']): ?>
                                <div class="aluno-info">
                                    <i class="fab fa-whatsapp"></i>
                                    <span><?= htmlspecialchars($equipe['professor_whatsapp']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if ($equipe['professor_instituicao']): ?>
                                <div class="aluno-info">
                                    <i class="fas fa-university"></i>
                                    <span><?= htmlspecialchars($equipe['professor_instituicao']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de Alunos -->
                <div class="equipe-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-graduate me-2"></i>Alunos da Equipe
                    </h3>
                    <?php if (count($alunos) === 0): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhum aluno cadastrado nesta equipe.
                        </div>
                    <?php else: ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <div class="aluno-card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="aluno-info">
                                            <i class="fas fa-user"></i>
                                            <strong><?= htmlspecialchars($aluno['nome']) ?></strong>
                                        </div>
                                        <div class="aluno-info">
                                            <i class="fas fa-envelope"></i>
                                            <span><?= htmlspecialchars($aluno['email']) ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($aluno['whatsapp']): ?>
                                        <div class="aluno-info">
                                            <i class="fab fa-whatsapp"></i>
                                            <span><?= htmlspecialchars($aluno['whatsapp']) ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="aluno-info">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Cadastrado em: <?= date('d/m/Y', strtotime($aluno['created_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Estatísticas -->
            <div class="stats-card">
                <div class="stats-number"><?= count($alunos) ?></div>
                <div class="stats-label">Alunos na Equipe</div>
            </div>
            
            <div class="stats-card">
                <div class="stats-number"><?= date_diff(date_create($equipe['created_at']), date_create())->days ?></div>
                <div class="stats-label">Dias de Existência</div>
            </div>
            
            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="editar-equipe.php?id=<?= $equipe_id ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Equipe
                        </a>
                        <a href="excluir-equipe.php?id=<?= $equipe_id ?>&desafio_id=<?= $equipe['desafio_id'] ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Tem certeza que deseja excluir esta equipe? Esta ação não pode ser desfeita.');">
                            <i class="fas fa-trash me-2"></i>Excluir Equipe
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>