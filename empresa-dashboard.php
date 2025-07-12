<?php
session_start();

if (!isset($_SESSION['empresa_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';
require_once 'models/Desafio.php';
require_once 'models/Empresa.php';

$database = new Database();
$db = $database->getConnection();
$desafio = new Desafio($db);
$empresa = new Empresa($db);

// Buscar dados da empresa
$empresa->id = $_SESSION['empresa_id'];
$empresa_data = $empresa->readOne();

// Buscar desafios da empresa
$desafios = $desafio->readByEmpresa($_SESSION['empresa_id']);

// Estatísticas
$total_desafios = $desafio->count();
$desafios_ativos = $desafio->countByStatus('ativo');
$desafios_em_andamento = $desafio->countByStatus('em_andamento');
$desafios_concluidos = $desafio->countByStatus('concluido');

include 'includes/header.php';
?>

<style>
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 0;
    margin-bottom: 20px;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 10px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    font-size: 2rem;
    margin-bottom: 8px;
}

.stats-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 3px;
}

.stats-label {
    color: #666;
    font-size: 0.8rem;
}

.desafio-card {
    background: white;
    border-radius: 15px;
    padding: 10px;
    margin-bottom: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    border-left: 4px solid #667eea;
}

.desafio-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.desafio-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 1.1rem;
}

.desafio-description {
    color: #666;
    margin-bottom: 10px;
    line-height: 1.4;
    font-size: 0.9rem;
}

.desafio-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.desafio-date {
    color: #999;
    font-size: 0.8rem;
}

.desafio-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-ativo {
    background: #e3f2fd;
    color: #1976d2;
}

.status-em_andamento {
    background: #fff3e0;
    color: #f57c00;
}

.status-concluido {
    background: #e8f5e8;
    color: #388e3c;
}

.btn-action {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    margin-right: 8px;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state-icon {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 20px;
}

.btn-cadastrar-desafio {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 15px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-cadastrar-desafio:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}
</style>

<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Dashboard da Empresa</h1>
                <p class="lead mb-0">Bem-vindo, <?php echo htmlspecialchars($_SESSION['empresa_nome']); ?>!</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="cadastrar-desafio.php" class="btn btn-cadastrar-desafio me-2">
                    <i class="fas fa-plus me-2"></i>Novo Desafio
                </a>
                <a href="logout.php" class="btn btn-outline-light" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Estatísticas -->
    <div class="row mb-3">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-primary">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="stats-number text-primary"><?php echo $total_desafios; ?></div>
                <div class="stats-label">Total de Desafios</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-success">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="stats-number text-success"><?php echo $desafios_ativos; ?></div>
                <div class="stats-label">Desafios Ativos</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number text-warning"><?php echo $desafios_em_andamento; ?></div>
                <div class="stats-label">Em Andamento</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number text-info"><?php echo $desafios_concluidos; ?></div>
                <div class="stats-label">Concluídos</div>
            </div>
        </div>
    </div>

    <!-- Lista de Desafios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Seus Desafios
                    </h5>
                    <a href="cadastrar-desafio.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Novo Desafio
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($desafios->rowCount() > 0): ?>
                        <?php while ($row = $desafios->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="desafio-card">
                                <div class="desafio-title">
                                    <?php echo htmlspecialchars($row['titulo']); ?>
                                </div>
                                <div class="desafio-description">
                                    <?php echo htmlspecialchars(substr($row['descricao_problema'], 0, 150)) . (strlen($row['descricao_problema']) > 150 ? '...' : ''); ?>
                                </div>
                                <div class="desafio-meta">
                                    <div class="desafio-date">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                    </div>
                                    <span class="desafio-status status-<?php echo $row['status']; ?>">
                                        <?php 
                                        echo $row['status'] == 'ativo' ? 'Ativo' : 
                                            ($row['status'] == 'em_andamento' ? 'Em Andamento' : 'Concluído'); 
                                        ?>
                                    </span>
                                </div>
                                <div class="desafio-actions">
                                    <a href="visualizar-desafio.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-outline-primary btn-action">
                                        <i class="fas fa-eye me-1"></i>Visualizar
                                    </a>
                                    <a href="editar-desafio.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-outline-warning btn-action">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </a>
                                    <a href="excluir-desafio.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-outline-danger btn-action"
                                       onclick="return confirm('Tem certeza que deseja excluir este desafio?')">
                                        <i class="fas fa-trash me-1"></i>Excluir
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h4>Nenhum desafio cadastrado ainda</h4>
                            <p class="text-muted">
                                Comece cadastrando seu primeiro desafio para conectar-se com talentos 
                                que podem ajudar sua empresa.
                            </p>
                            <a href="cadastrar-desafio.php" class="btn btn-cadastrar-desafio">
                                <i class="fas fa-plus me-2"></i>Cadastrar Primeiro Desafio
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 