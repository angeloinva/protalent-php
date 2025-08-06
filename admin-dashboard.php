<?php
session_start();

// Verificar se é administrador
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';
require_once 'models/Desafio.php';
require_once 'models/Empresa.php';
require_once 'models/User.php';
require_once 'models/Professor.php';
require_once 'models/Talent.php';

$database = new Database();
$db = $database->getConnection();
$desafio = new Desafio($db);
$empresa = new Empresa($db);
$user = new User($db);
$professor = new Professor($db);
$talent = new Talent($db);

// Processar filtros
$filtro_empresa = $_GET['empresa'] ?? '';
$filtro_cidade = $_GET['cidade'] ?? '';
$filtro_desafio = $_GET['desafio'] ?? '';
$filtro_trl = $_GET['trl'] ?? '';

// Buscar todos os desafios com filtros
$desafios = $desafio->readWithFilters($filtro_empresa, $filtro_cidade, $filtro_desafio, '', $filtro_trl);

// Estatísticas
$total_desafios = $desafio->count();
$total_empresas = $empresa->count();
$total_professores = $professor->countInteressados(); // Apenas professores interessados
$total_professores_cadastrados = $professor->count(); // Todos os professores cadastrados
// Contar total de alunos (talentos)
$stmtTalentos = $db->prepare('SELECT COUNT(*) as total FROM alunos');
$stmtTalentos->execute();
$total_talentos = $stmtTalentos->fetchColumn();

// Buscar empresas para filtro
$empresas = $empresa->read();

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

.admin-actions {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    margin-top: 10px;
    border-left: 3px solid #dc3545;
}

.admin-actions h6 {
    color: #dc3545;
    margin-bottom: 8px;
    font-size: 0.9rem;
}
</style>

<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Dashboard Administrativo</h1>
                <p class="lead mb-0">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>
            <div class="col-md-4 text-end">
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
        <div class="col-12 col-md mb-3">
            <div class="stats-card">
                <div class="stats-icon text-primary">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="stats-number text-primary"><?php echo $total_desafios; ?></div>
                <div class="stats-label">Total de Desafios</div>
            </div>
        </div>
        <div class="col-12 col-md mb-3">
            <div class="stats-card" style="cursor:pointer;" onclick="window.location.href='empresas-lista.php'">
                <div class="stats-icon text-success">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stats-number text-success"><?php echo $total_empresas; ?></div>
                <div class="stats-label">Total de Empresas</div>
                <small class="text-muted">Clique para ver detalhes</small>
            </div>
        </div>
        <div class="col-12 col-md mb-3">
            <div class="stats-card" style="cursor: pointer;" onclick="window.location.href='professores-interessados.php'">
                <div class="stats-icon text-info">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stats-number text-info"><?php echo $total_professores; ?></div>
                <div class="stats-label">Professores Interessados</div>
                <small class="text-muted">Clique para ver detalhes</small>
            </div>
        </div>
        <div class="col-12 col-md mb-3">
            <div class="stats-card">
                <div class="stats-icon text-secondary">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stats-number text-secondary"><?php echo $total_professores_cadastrados; ?></div>
                <div class="stats-label">Professores Cadastrados</div>
            </div>
        </div>
        <div class="col-12 col-md mb-3">
            <div class="stats-card" style="cursor:pointer;" onclick="window.location.href='alunos-lista.php'">
                <div class="stats-icon text-warning">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stats-number text-warning"><?php echo $total_talentos; ?></div>
                <div class="stats-label">Talentos Cadastrados</div>
                <small class="text-muted">Clique para ver detalhes</small>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2"></i>Filtros Avançados
        </h5>
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <select class="form-control" name="empresa">
                    <option value="">Todas as Empresas</option>
                    <?php while ($emp = $empresas->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $emp['id']; ?>" 
                                <?php echo $filtro_empresa == $emp['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($emp['nome_fantasia'] ?: $emp['razao_social']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="cidade" 
                       placeholder="Filtrar por cidade" 
                       value="<?php echo htmlspecialchars($filtro_cidade); ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="desafio" 
                       placeholder="Buscar por título do desafio" 
                       value="<?php echo htmlspecialchars($filtro_desafio); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="trl">
                    <option value="">Todos os níveis TRL</option>
                    <option value="TRL 1" <?php echo $filtro_trl == 'TRL 1' ? 'selected' : ''; ?>>TRL 1 - Princípios básicos</option>
                    <option value="TRL 2" <?php echo $filtro_trl == 'TRL 2' ? 'selected' : ''; ?>>TRL 2 - Conceito tecnológico</option>
                    <option value="TRL 3" <?php echo $filtro_trl == 'TRL 3' ? 'selected' : ''; ?>>TRL 3 - Prova de conceito</option>
                    <option value="TRL 4" <?php echo $filtro_trl == 'TRL 4' ? 'selected' : ''; ?>>TRL 4 - Validação em laboratório</option>
                    <option value="TRL 5" <?php echo $filtro_trl == 'TRL 5' ? 'selected' : ''; ?>>TRL 5 - Validação em ambiente relevante</option>
                    <option value="TRL 6" <?php echo $filtro_trl == 'TRL 6' ? 'selected' : ''; ?>>TRL 6 - Demonstração em ambiente relevante</option>
                    <option value="TRL 7" <?php echo $filtro_trl == 'TRL 7' ? 'selected' : ''; ?>>TRL 7 - Protótipo em ambiente operacional</option>
                    <option value="TRL 8" <?php echo $filtro_trl == 'TRL 8' ? 'selected' : ''; ?>>TRL 8 - Sistema completo qualificado</option>
                    <option value="TRL 9" <?php echo $filtro_trl == 'TRL 9' ? 'selected' : ''; ?>>TRL 9 - Sistema real comprovado</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
            </div>
        </form>
        <?php if ($filtro_empresa || $filtro_cidade || $filtro_desafio || $filtro_trl): ?>
            <div class="mt-3">
                <a href="admin-dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-2"></i>Limpar Filtros
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Lista de Desafios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Todos os Desafios
                        <?php if ($filtro_empresa || $filtro_cidade || $filtro_desafio || $filtro_trl): ?>
                            <small class="text-muted">(Filtrados)</small>
                        <?php endif; ?>
                    </h5>
                    <span class="badge bg-primary"><?php echo $desafios->rowCount(); ?> desafios</span>
                </div>
                <div class="card-body">
                    <?php if ($desafios->rowCount() > 0): ?>
                        <?php while ($row = $desafios->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="desafio-card">
                                <div class="desafio-title">
                                    <?php echo htmlspecialchars($row['titulo']); ?>
                                </div>
                                <div class="desafio-empresa">
                                    <i class="fas fa-building me-1"></i>
                                    <?php echo htmlspecialchars($row['empresa_nome'] ?: 'Empresa não informada'); ?>
                                    <?php if ($row['cidade']): ?>
                                        <span class="text-muted ms-2">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($row['cidade']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="desafio-description">
                                    <?php echo htmlspecialchars(substr($row['descricao_problema'], 0, 200)) . (strlen($row['descricao_problema']) > 200 ? '...' : ''); ?>
                                </div>
                                <div class="desafio-meta">
                                    <div class="desafio-date">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($row['nivel_trl']): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-layer-group me-1"></i>
                                                <?php echo htmlspecialchars($row['nivel_trl']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php 
                                        $stmtEq = $db->prepare('SELECT COUNT(*) FROM equipes WHERE desafio_id = ?');
                                        $stmtEq->execute([$row['id']]);
                                        $qtde_equipes = $stmtEq->fetchColumn();
                                        ?>
                                        <span class="desafio-status status-<?php echo $row['status']; ?>">
                                            <?php 
                                            if ($row['status'] == 'ativo') {
                                                echo $qtde_equipes . ' equipe' . ($qtde_equipes == 1 ? '' : 's');
                                            } else {
                                                echo $row['status'] == 'em_andamento' ? 'Em Andamento' : 'Concluído';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="desafio-actions">
                                    <a href="visualizar-desafio.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-outline-primary btn-action">
                                        <i class="fas fa-eye me-1"></i>Ver Detalhes
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
                        <div class="text-center py-5">
                            <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                            <h5>Nenhum desafio encontrado</h5>
                            <p class="text-muted">
                                <?php if ($filtro_empresa || $filtro_cidade || $filtro_desafio || $filtro_trl): ?>
                                    Tente ajustar os filtros de pesquisa.
                                <?php else: ?>
                                    Ainda não há desafios cadastrados no sistema.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 