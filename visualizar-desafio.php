<?php
session_start();

require_once 'config/database.php';
require_once 'models/Desafio.php';
require_once 'models/Empresa.php';

$database = new Database();
$db = $database->getConnection();
$desafio = new Desafio($db);

$desafio_id = $_GET['id'] ?? 0;
if (!$desafio_id) {
    header("Location: index.php");
    exit();
}

$desafio->id = $desafio_id;
$desafio_data = $desafio->readOne();

if (!$desafio_data) {
    header("Location: index.php");
    exit();
}

include 'includes/header.php';
?>

<style>
.desafio-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    margin-bottom: 30px;
}

.desafio-content {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.desafio-title {
    color: #333;
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.desafio-empresa {
    color: #667eea;
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 20px;
}

.desafio-meta {
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

.desafio-section {
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

.section-content {
    color: #666;
    line-height: 1.6;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.contact-section {
    background: #e3f2fd;
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 30px;
}

.contact-title {
    color: #1976d2;
    font-weight: 600;
    margin-bottom: 15px;
}

.contact-info {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.contact-info i {
    margin-right: 10px;
    color: #1976d2;
    width: 20px;
}

.btn-contact {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin-top: 15px;
}

.btn-contact:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.status-badge {
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 0.9rem;
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

.requisitos-section {
    background: #fff3e0;
    border-radius: 8px;
    padding: 20px;
    border-left: 4px solid #f57c00;
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
</style>

<div class="desafio-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Detalhes do Desafio</h1>
                <p class="lead mb-0">Conheça mais sobre este projeto</p>
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
            <div class="desafio-content">
                <h2 class="desafio-title"><?php echo htmlspecialchars($desafio_data['titulo']); ?></h2>
                
                <div class="desafio-empresa">
                    <i class="fas fa-building me-2"></i>
                    <?php echo htmlspecialchars($desafio_data['empresa_nome'] ?: $desafio_data['razao_social']); ?>
                </div>
                
                <div class="desafio-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Cadastrado em: <?php echo date('d/m/Y', strtotime($desafio_data['created_at'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>Mentor: <?php echo htmlspecialchars($desafio_data['mentor']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="status-badge status-<?php echo $desafio_data['status']; ?>">
                            <?php 
                            echo $desafio_data['status'] == 'ativo' ? 'Ativo' : 
                                ($desafio_data['status'] == 'em_andamento' ? 'Em Andamento' : 'Concluído'); 
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="desafio-section">
                    <h3 class="section-title">Descrição do Problema</h3>
                    <div class="section-content">
                        <?php echo nl2br(htmlspecialchars($desafio_data['descricao_problema'])); ?>
                    </div>
                </div>
                
                <?php if ($desafio_data['pesquisado']): ?>
                <div class="desafio-section">
                    <h3 class="section-title">Pesquisa Prévia</h3>
                    <div class="section-content">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        A empresa já pesquisou ou iniciou o desenvolvimento de alguma solução para este problema.
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($desafio_data['requisitos_especificos'] && $desafio_data['descricao_requisitos']): ?>
                <div class="desafio-section">
                    <h3 class="section-title">Requisitos Específicos</h3>
                    <div class="requisitos-section">
                        <?php echo nl2br(htmlspecialchars($desafio_data['descricao_requisitos'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="contact-section">
                <h3 class="contact-title">
                    <i class="fas fa-phone me-2"></i>Informações de Contato
                </h3>
                
                <div class="contact-info">
                    <i class="fas fa-user"></i>
                    <span><strong>Mentor:</strong> <?php echo htmlspecialchars($desafio_data['mentor']); ?></span>
                </div>
                
                <?php if ($desafio_data['whatsapp']): ?>
                <div class="contact-info">
                    <i class="fab fa-whatsapp"></i>
                    <span><strong>WhatsApp:</strong> <?php echo htmlspecialchars($desafio_data['whatsapp']); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="text-center">
                    <?php if ($desafio_data['whatsapp']): ?>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $desafio_data['whatsapp']); ?>?text=Olá! Tenho interesse no desafio: <?php echo urlencode($desafio_data['titulo']); ?>" 
                           target="_blank" class="btn btn-contact">
                            <i class="fab fa-whatsapp me-2"></i>Contatar via WhatsApp
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            WhatsApp não informado para contato.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Como Participar
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li>Analise o problema descrito</li>
                        <li>Verifique se tem interesse e capacidade</li>
                        <li>Entre em contato com o mentor</li>
                        <li>Apresente sua proposta de solução</li>
                        <li>Aguarde a aprovação da empresa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 