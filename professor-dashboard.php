<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['professor_id'])) {
    header('Location: login.php');
    exit();
}

$professor_id = $_SESSION['professor_id'];
$database = new Database();
$db = $database->getConnection();

// Buscar equipes do professor e contar alunos
$equipes = [];
$total_alunos = 0;
$stmt = $db->prepare('SELECT equipes.id, equipes.nome AS equipe_nome, desafios.titulo AS desafio_titulo, desafios.id AS desafio_id, desafios.nivel_trl, desafios.status, desafios.created_at, e.nome_fantasia AS empresa_nome, e.razao_social
FROM equipes 
JOIN desafios ON equipes.desafio_id = desafios.id 
LEFT JOIN empresas e ON desafios.empresa_id = e.id
WHERE equipes.professor_id = ?');
$stmt->execute([$professor_id]);
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ids_equipes = array_column($equipes, 'id');
if ($ids_equipes) {
    $in = implode(',', array_fill(0, count($ids_equipes), '?'));
    $stmtAlunos = $db->prepare('SELECT COUNT(*) FROM alunos WHERE equipe_id IN (' . $in . ')');
    $stmtAlunos->execute($ids_equipes);
    $total_alunos = $stmtAlunos->fetchColumn();
} else {
    $total_alunos = 0;
}
$total_equipes = count($equipes);
$total_desafios_participando = count(array_unique(array_column($equipes, 'desafio_id')));

// Filtros para desafios disponíveis
$filtro_cidade = $_GET['cidade'] ?? '';
$filtro_trl = $_GET['trl'] ?? '';
$filtro_empresa = $_GET['empresa'] ?? '';
$filtro_data_ini = $_GET['data_ini'] ?? '';
$filtro_data_fim = $_GET['data_fim'] ?? '';
// Buscar empresas para filtro
$empresas = $db->query('SELECT id, nome_fantasia, razao_social FROM empresas ORDER BY nome_fantasia, razao_social')->fetchAll(PDO::FETCH_ASSOC);
// Buscar desafios disponíveis para participar (com filtros)
$ids_desafios_participando = array_column($equipes, 'desafio_id');
$sql = 'SELECT d.id, d.titulo, d.descricao_problema, d.created_at, d.nivel_trl, d.status, e.nome_fantasia AS empresa_nome, e.razao_social, e.cidade AS cidade FROM desafios d LEFT JOIN empresas e ON d.empresa_id = e.id WHERE d.status = "ativo"';
$params = [];
if ($ids_desafios_participando) {
    $in = implode(',', array_fill(0, count($ids_desafios_participando), '?'));
    $sql .= ' AND d.id NOT IN (' . $in . ')';
    $params = array_merge($params, $ids_desafios_participando);
}
if ($filtro_cidade) {
    $sql .= ' AND e.cidade LIKE ?';
    $params[] = "%$filtro_cidade%";
}
if ($filtro_trl) {
    $sql .= ' AND d.nivel_trl = ?';
    $params[] = $filtro_trl;
}
if ($filtro_empresa) {
    $sql .= ' AND e.id = ?';
    $params[] = $filtro_empresa;
}
if ($filtro_data_ini) {
    $sql .= ' AND d.created_at >= ?';
    $params[] = $filtro_data_ini . ' 00:00:00';
}
if ($filtro_data_fim) {
    $sql .= ' AND d.created_at <= ?';
    $params[] = $filtro_data_fim . ' 23:59:59';
}
$sql .= ' ORDER BY d.created_at DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$desafios_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
.desafio-empresa {
    color: #667eea;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 0.9rem;
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
    flex-wrap: wrap;
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
.filters-section {
    background: white;
    border-radius: 15px;
    padding: 12px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Dashboard do Professor</h1>
                <p class="lead mb-0">Bem-vindo, <?= htmlspecialchars($_SESSION['professor_nome'] ?? '') ?>!</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="logout.php" class="btn btn-outline-light" title="Sair">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <!-- Estatísticas -->
    <div class="row mb-3">
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number text-primary"><?= $total_equipes ?></div>
                <div class="stats-label">Equipes</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-success">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stats-number text-success"><?= $total_alunos ?></div>
                <div class="stats-label">Alunos nas Equipes</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-warning">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="stats-number text-warning"><?= $total_desafios_participando ?></div>
                <div class="stats-label">Desafios Participando</div>
            </div>
        </div>
    </div>

    <!-- Desafios Participando -->
    <h4 class="mb-3">Desafios que estou participando</h4>
    <?php if (count($equipes) === 0): ?>
        <p>Você ainda não cadastrou nenhuma equipe.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($equipes as $row): ?>
                <div class="col-md-6 mb-4">
                    <div class="desafio-card">
                        <div class="desafio-title">
                            <?= htmlspecialchars($row['desafio_titulo']); ?>
                        </div>
                        <div class="desafio-empresa">
                            <i class="fas fa-users me-1"></i> Equipe: <?= htmlspecialchars($row['equipe_nome']); ?>
                            <br><i class="fas fa-building me-1"></i> <?= htmlspecialchars($row['empresa_nome'] ?: $row['razao_social'] ?: 'Empresa não informada'); ?>
                        </div>
                        <div class="desafio-meta">
                            <div class="desafio-date">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date('d/m/Y', strtotime($row['created_at'])); ?>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($row['nivel_trl']): ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-layer-group me-1"></i>
                                        <?= htmlspecialchars($row['nivel_trl']); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="desafio-status status-<?= $row['status']; ?>">
                                    <?php 
                                    echo $row['status'] == 'ativo' ? 'Ativo' : 
                                        ($row['status'] == 'em_andamento' ? 'Em Andamento' : 'Concluído'); 
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="desafio-actions">
                            <a href="desafio-andamento.php?equipe_id=<?= $row['id'] ?>&desafio_id=<?= $row['desafio_id'] ?>" class="btn btn-success btn-action">Gerenciar desafio</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Desafios Disponíveis -->
    <h4 class="mb-3">Desafios Disponíveis para Participar</h4>
    <div class="filters-section mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Cidade</label>
                <input type="text" class="form-control" name="cidade" value="<?= htmlspecialchars($filtro_cidade) ?>" placeholder="Digite a cidade">
            </div>
            <div class="col-md-2">
                <label class="form-label">TRL</label>
                <select class="form-control" name="trl">
                    <option value="">Todos</option>
                    <option value="TRL 1" <?= $filtro_trl == 'TRL 1' ? 'selected' : '' ?>>TRL 1</option>
                    <option value="TRL 2" <?= $filtro_trl == 'TRL 2' ? 'selected' : '' ?>>TRL 2</option>
                    <option value="TRL 3" <?= $filtro_trl == 'TRL 3' ? 'selected' : '' ?>>TRL 3</option>
                    <option value="TRL 4" <?= $filtro_trl == 'TRL 4' ? 'selected' : '' ?>>TRL 4</option>
                    <option value="TRL 5" <?= $filtro_trl == 'TRL 5' ? 'selected' : '' ?>>TRL 5</option>
                    <option value="TRL 6" <?= $filtro_trl == 'TRL 6' ? 'selected' : '' ?>>TRL 6</option>
                    <option value="TRL 7" <?= $filtro_trl == 'TRL 7' ? 'selected' : '' ?>>TRL 7</option>
                    <option value="TRL 8" <?= $filtro_trl == 'TRL 8' ? 'selected' : '' ?>>TRL 8</option>
                    <option value="TRL 9" <?= $filtro_trl == 'TRL 9' ? 'selected' : '' ?>>TRL 9</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Empresa</label>
                <select class="form-control" name="empresa">
                    <option value="">Todas</option>
                    <?php foreach ($empresas as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= $filtro_empresa == $emp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($emp['nome_fantasia'] ?: $emp['razao_social']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Data Inicial</label>
                <input type="date" class="form-control" name="data_ini" value="<?= htmlspecialchars($filtro_data_ini) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Data Final</label>
                <input type="date" class="form-control" name="data_fim" value="<?= htmlspecialchars($filtro_data_fim) ?>">
            </div>
            <div class="col-md-12 text-end mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
                <?php if ($filtro_cidade || $filtro_trl || $filtro_empresa || $filtro_data_ini || $filtro_data_fim): ?>
                    <a href="professor-dashboard.php" class="btn btn-outline-secondary ms-2">Limpar Filtros</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Desafios Disponíveis
                    </h5>
                    <span class="badge bg-primary"><?= count($desafios_disponiveis) ?> desafios</span>
                </div>
                <div class="card-body">
                    <?php if (count($desafios_disponiveis) > 0): ?>
                        <?php foreach ($desafios_disponiveis as $row): ?>
                            <div class="desafio-card">
                                <div class="desafio-title">
                                    <?= htmlspecialchars($row['titulo']); ?>
                                </div>
                                <div class="desafio-empresa">
                                    <i class="fas fa-building me-1"></i>
                                    <?= htmlspecialchars($row['empresa_nome'] ?: $row['razao_social'] ?: 'Empresa não informada'); ?>
                                </div>
                                <div class="desafio-description">
                                    <?= htmlspecialchars(substr($row['descricao_problema'], 0, 200)) . (strlen($row['descricao_problema']) > 200 ? '...' : ''); ?>
                                </div>
                                <div class="desafio-meta">
                                    <div class="desafio-date">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d/m/Y', strtotime($row['created_at'])); ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($row['nivel_trl']): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-layer-group me-1"></i>
                                                <?= htmlspecialchars($row['nivel_trl']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="desafio-status status-<?= $row['status']; ?>">
                                            <?php 
                                            echo $row['status'] == 'ativo' ? 'Ativo' : 
                                                ($row['status'] == 'em_andamento' ? 'Em Andamento' : 'Concluído'); 
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="desafio-actions">
                                    <a href="visualizar-desafio.php?id=<?= $row['id']; ?>" class="btn btn-outline-primary btn-action">
                                        <i class="fas fa-eye me-1"></i>Ver Detalhes
                                    </a>
                                    <a href="cadastrar-equipe.php?desafio_id=<?= $row['id'] ?>" class="btn btn-primary btn-action">Participar do desafio</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                            <h5>Nenhum desafio disponível no momento</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?> 