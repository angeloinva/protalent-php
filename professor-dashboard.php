<?php
session_start();

require_once 'config/database.php';
require_once 'models/Desafio.php';
require_once 'models/Empresa.php';
require_once 'models/Professor.php';

$database = new Database();
$db = $database->getConnection();
$desafio = new Desafio($db);
$empresa = new Empresa($db);
$professor = new Professor($db);

// Buscar todos os desafios
$desafios = $desafio->read();

// Estatísticas
$total_desafios = $desafio->count();
$total_empresas = $empresa->count();

// Processar pesquisa
$termo_pesquisa = $_GET['pesquisa'] ?? '';
$filtro_empresa = $_GET['empresa'] ?? '';
$filtro_cidade = $_GET['cidade'] ?? '';
$filtro_trl = $_GET['trl'] ?? '';

if ($termo_pesquisa || $filtro_empresa || $filtro_cidade || $filtro_trl) {
    // Usar o método readWithFilters atualizado que inclui filtro por TRL
    $desafios = $desafio->readWithFilters('', $filtro_cidade, $termo_pesquisa, $filtro_empresa, $filtro_trl);
}

// Processar interesse em edições futuras
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['interesse_edicoes'])) {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    
    if ($nome && $email) {
        // Verificar se já existe um professor com este email
        $professor_existente = $professor->readByEmail($email);
        
        if ($professor_existente) {
            // Atualizar interesse do professor existente
            if ($professor->updateInteresseEdicoesFuturas($email, 1)) {
                $success_message = 'Interesse atualizado com sucesso! Em breve entraremos em contato.';
            } else {
                $error_message = 'Erro ao atualizar interesse. Tente novamente.';
            }
        } else {
            // Criar novo professor interessado
            $professor->nome = $nome;
            $professor->email = $email;
            $professor->whatsapp = $whatsapp;
            $professor->instituicao = ''; // Pode ser preenchido depois
            $professor->area_atuacao = ''; // Pode ser preenchido depois
            $professor->interessado_edicoes_futuras = 1;
            
            if ($professor->create()) {
                $success_message = 'Interesse registrado com sucesso! Em breve entraremos em contato.';
            } else {
                $error_message = 'Erro ao registrar interesse. Tente novamente.';
            }
        }
    } else {
        $error_message = 'Por favor, preencha pelo menos o nome e email.';
    }
}

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

.search-section {
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

.desafio-location {
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

.timeline-section {
    background: white;
    border-radius: 15px;
    padding: 12px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.timeline {
    position: relative;
    padding: 15px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #667eea;
    transform: translateX(-50%);
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    width: 50%;
}

.timeline-item:nth-child(odd) {
    left: 0;
    padding-right: 30px;
}

.timeline-item:nth-child(even) {
    left: 50%;
    padding-left: 30px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    top: 20px;
    width: 12px;
    height: 12px;
    background: #667eea;
    border-radius: 50%;
}

.timeline-item:nth-child(odd)::before {
    right: -6px;
}

.timeline-item:nth-child(even)::before {
    left: -6px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    border-left: 3px solid #667eea;
}

.timeline-date {
    font-weight: 600;
    color: #667eea;
    margin-bottom: 5px;
}

.timeline-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-description {
    color: #666;
    font-size: 0.9rem;
}

.interesse-section {
    background: white;
    border-radius: 15px;
    padding: 12px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-align: center;
}

.btn-interesse {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-interesse:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}
</style>

<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Dashboard do Professor</h1>
                <p class="lead mb-0">Explore desafios reais das empresas e conecte-se com oportunidades</p>
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
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="stats-number text-primary"><?php echo $total_desafios; ?></div>
                <div class="stats-label">Desafios Disponíveis</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-success">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stats-number text-success"><?php echo $total_empresas; ?></div>
                <div class="stats-label">Empresas Participantes</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number text-warning">∞</div>
                <div class="stats-label">Oportunidades</div>
            </div>
        </div>
    </div>

    <!-- Seção de Pesquisa -->
    <div class="search-section">
        <h5 class="mb-3">
            <i class="fas fa-search me-2"></i>Pesquisar Desafios
        </h5>
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="pesquisa" 
                       placeholder="Pesquisar por título ou descrição..." 
                       value="<?php echo htmlspecialchars($termo_pesquisa); ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="empresa" 
                       placeholder="Filtrar por empresa" 
                       value="<?php echo htmlspecialchars($filtro_empresa); ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="cidade" 
                       placeholder="Filtrar por cidade" 
                       value="<?php echo htmlspecialchars($filtro_cidade); ?>">
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
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Cronograma do Projeto -->
    <div class="timeline-section">
        <h5 class="mb-3" style="cursor: pointer;" onclick="toggleTimeline()">
            <i class="fas fa-calendar-alt me-2"></i>Cronograma do Projeto
            <i class="fas fa-chevron-down ms-2" id="timeline-icon"></i>
        </h5>
        <div class="timeline" id="timeline-content" style="display: none;">
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">Semana 1-2</div>
                    <div class="timeline-title">Entendimento do desafio</div>
                    <div class="timeline-description">Entendimento do problema e agenda de kick-off</div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">Semana 3-7</div>
                    <div class="timeline-title">Desenvolvimento da solução</div>
                    <div class="timeline-description">Metodologias ágeis e início do desenvolvimento da solução proposta</div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">Semana 8-9</div>
                    <div class="timeline-title">Banca parcial</div>
                    <div class="timeline-description">Apresentação da solução para a empresa</div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="timeline-date">Semana 12</div>
                    <div class="timeline-title">Entrega final da solução</div>
                    <div class="timeline-description">Apresentação da solução para a empresa, seja presencial ou em vídeo</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interesse em Edições Futuras -->
    <div class="interesse-section">
        <h5 class="mb-3">
            <i class="fas fa-bell me-2"></i>Interesse em Edições Futuras
        </h5>
        <p class="text-muted mb-3">
            Cadastre seu interesse para receber notificações sobre novos desafios e oportunidades
        </p>
        <form method="POST" class="row justify-content-center">
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="nome" 
                               placeholder="Seu nome completo" required>
                    </div>
                    <div class="col-md-4">
                        <input type="email" class="form-control" name="email" 
                               placeholder="Seu email" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="whatsapp" 
                               placeholder="WhatsApp (11) 99999-9999">
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" name="interesse_edicoes" class="btn btn-interesse">
                        <i class="fas fa-check me-2"></i>Cadastrar Interesse
                    </button>
                </div>
            </div>
        </form>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success mt-3">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-3">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Lista de Desafios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Desafios Disponíveis
                    </h5>
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
                                        <span class="desafio-status status-<?php echo $row['status']; ?>">
                                            <?php 
                                            echo $row['status'] == 'ativo' ? 'Ativo' : 
                                                ($row['status'] == 'em_andamento' ? 'Em Andamento' : 'Concluído'); 
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="desafio-actions">
                                    <a href="visualizar-desafio.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-outline-primary btn-action">
                                        <i class="fas fa-eye me-1"></i>Ver Detalhes
                                    </a>
                                    <?php if ($row['whatsapp']): ?>
                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $row['whatsapp']); ?>?text=Olá! Tenho interesse no desafio: <?php echo urlencode($row['titulo']); ?>" 
                                           target="_blank" class="btn btn-outline-success btn-action">
                                            <i class="fab fa-whatsapp me-1"></i>Contatar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                            <h5>Nenhum desafio disponível no momento</h5>
                            <p class="text-muted">Volte em breve para ver novos desafios!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTimeline() {
    const timelineContent = document.getElementById('timeline-content');
    const timelineIcon = document.getElementById('timeline-icon');
    
    if (timelineContent.style.display === 'none') {
        timelineContent.style.display = 'block';
        timelineIcon.className = 'fas fa-chevron-up ms-2';
    } else {
        timelineContent.style.display = 'none';
        timelineIcon.className = 'fas fa-chevron-down ms-2';
    }
}

// Máscara para WhatsApp no formulário de interesse
document.addEventListener('DOMContentLoaded', function() {
    const whatsappInput = document.querySelector('input[name="whatsapp"]');
    if (whatsappInput) {
        whatsappInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?> 