<?php
session_start();
require_once 'config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database = new Database();
$db = $database->getConnection();

// Filtros
$filtro_cidade = $_GET['cidade'] ?? '';
$filtro_trl = $_GET['trl'] ?? '';
$filtro_empresa = $_GET['empresa'] ?? '';
$filtro_data_ini = $_GET['data_ini'] ?? '';
$filtro_data_fim = $_GET['data_fim'] ?? '';

// Buscar empresas para filtro
$empresas = $db->query('SELECT id, nome_fantasia, razao_social FROM empresas ORDER BY nome_fantasia, razao_social')->fetchAll(PDO::FETCH_ASSOC);

// Montar SQL com filtros
$sql = 'SELECT d.id, d.titulo, d.descricao_problema, d.created_at, d.nivel_trl, d.status, e.nome_fantasia AS empresa_nome, e.razao_social, e.cidade AS cidade FROM desafios d LEFT JOIN empresas e ON d.empresa_id = e.id WHERE d.status = "ativo"';
$params = [];
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
$desafios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<!-- DESAFIOS: ' . count($desafios) . ' -->';

echo '<!-- HEADER INCLUÍDO -->';

include 'includes/header.php';
?>
<style>
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 0;
    margin-bottom: 20px;
}
.filters-section {
    background: white;
    border-radius: 15px;
    padding: 12px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
</style>
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Desafios para Professores</h1>
                <p class="lead mb-0">Explore desafios reais das empresas e participe com sua equipe!</p>
            </div>
        </div>
    </div>
</div>
<div class="container">
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
                    <option value="TRL 1" <?= $filtro_trl == 'TRL 1' ? 'selected' : '' ?>>TRL 1 - Princípios básicos</option>
                    <option value="TRL 2" <?= $filtro_trl == 'TRL 2' ? 'selected' : '' ?>>TRL 2 - Conceito tecnológico</option>
                    <option value="TRL 3" <?= $filtro_trl == 'TRL 3' ? 'selected' : '' ?>>TRL 3 - Prova de conceito</option>
                    <option value="TRL 4" <?= $filtro_trl == 'TRL 4' ? 'selected' : '' ?>>TRL 4 - Validação em laboratório</option>
                    <option value="TRL 5" <?= $filtro_trl == 'TRL 5' ? 'selected' : '' ?>>TRL 5 - Validação em ambiente relevante</option>
                    <option value="TRL 6" <?= $filtro_trl == 'TRL 6' ? 'selected' : '' ?>>TRL 6 - Demonstração em ambiente relevante</option>
                    <option value="TRL 7" <?= $filtro_trl == 'TRL 7' ? 'selected' : '' ?>>TRL 7 - Protótipo em ambiente operacional</option>
                    <option value="TRL 8" <?= $filtro_trl == 'TRL 8' ? 'selected' : '' ?>>TRL 8 - Sistema completo qualificado</option>
                    <option value="TRL 9" <?= $filtro_trl == 'TRL 9' ? 'selected' : '' ?>>TRL 9 - Sistema real comprovado</option>
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
                    <a href="desafios-professor.php" class="btn btn-outline-secondary ms-2">Limpar Filtros</a>
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
                    <span class="badge bg-primary"><?= count($desafios) ?> desafios</span>
                </div>
                <div class="card-body">
                    <?php if (count($desafios) > 0): ?>
                        <?php foreach ($desafios as $row): ?>
                            <div class="desafio-card">
                                <div class="desafio-title">
                                    <?= htmlspecialchars($row['titulo']); ?>
                                </div>
                                <div class="desafio-empresa">
                                    <i class="fas fa-building me-1"></i>
                                    <?= htmlspecialchars($row['empresa_nome'] ?: $row['razao_social'] ?: 'Empresa não informada'); ?>
                                    <?php if ($row['cidade']): ?>
                                        <span class="text-muted ms-2">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?= htmlspecialchars($row['cidade']); ?>
                                        </span>
                                    <?php endif; ?>
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
                                            if ($row['status'] == 'ativo') {
                                                $stmtEq = $db->prepare('SELECT COUNT(*) FROM equipes WHERE desafio_id = ?');
                                                $stmtEq->execute([$row['id']]);
                                                $qtde_equipes = $stmtEq->fetchColumn();
                                                echo $qtde_equipes . ' equipe' . ($qtde_equipes == 1 ? '' : 's');
                                            } else {
                                                echo $row['status'] == 'em_andamento' ? 'Em Andamento' : 'Concluído';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="desafio-actions">
                                    <a href="visualizar-desafio.php?id=<?= $row['id']; ?>" class="btn btn-outline-primary btn-action">
                                        <i class="fas fa-eye me-1"></i>Ver Detalhes
                                    </a>
                                    <?php if (isset($_SESSION['professor_id'])): ?>
                                        <a href="cadastrar-equipe.php?desafio_id=<?= $row['id'] ?>" class="btn btn-primary btn-action">Participar do desafio</a>
                                    <?php else: ?>
                                        <a href="login.php?redirect=cadastrar-equipe.php?desafio_id=<?= $row['id'] ?>" class="btn btn-primary btn-action">Participar do desafio</a>
                                    <?php endif; ?>
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