<?php
session_start();

if (!isset($_SESSION['empresa_id'])) {
    header("Location: index.php");
    exit();
}

include 'includes/header.php';
?>

<style>
.success-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.success-content {
    background: white;
    border-radius: 15px;
    padding: 40px;
    margin-top: -40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    z-index: 10;
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 20px;
}

.success-title {
    color: #333;
    margin-bottom: 20px;
}

.success-message {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 30px;
}

.btn-success-action {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    margin: 10px;
}

.btn-success-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-outline-success-action {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    margin: 10px;
}

.btn-outline-success-action:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.next-steps {
    padding: 60px 0;
    background: #f8f9fa;
}

.step-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    height: 100%;
    transition: transform 0.3s ease;
}

.step-card:hover {
    transform: translateY(-5px);
}

.step-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    margin: 0 auto 20px;
}

.step-title {
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.step-description {
    color: #666;
    line-height: 1.6;
}
</style>

<div class="success-section">
    <div class="container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="display-4 fw-bold">Desafio Cadastrado!</h1>
        <p class="lead">Obrigado por cadastrar seu desafio no ProTalent</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="success-content">
                <h2 class="success-title">Parabéns!</h2>
                <p class="success-message">
                    Seu desafio foi cadastrado com sucesso e agora está disponível para que professores 
                    e alunos possam visualizar e propor soluções. Em breve você receberá notificações 
                    sobre interessados em trabalhar no seu projeto.
                </p>
                
                <div class="text-center">
                    <a href="empresa-dashboard.php" class="btn btn-success-action">
                        <i class="fas fa-tachometer-alt me-2"></i>Voltar ao Dashboard
                    </a>
                    <a href="cadastrar-desafio.php" class="btn btn-outline-success-action">
                        <i class="fas fa-plus me-2"></i>Cadastrar Outro Desafio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="next-steps">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold">O que acontece agora?</h2>
                <p class="lead text-muted">Acompanhe o processo do seu desafio</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Visualização</h3>
                    <p class="step-description">
                        Professores e alunos de universidades podem visualizar seu desafio 
                        e avaliar se têm interesse e capacidade de desenvolvê-lo.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Propostas</h3>
                    <p class="step-description">
                        Talentos interessados entrarão em contato através do WhatsApp 
                        informado para apresentar suas propostas de solução.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Desenvolvimento</h3>
                    <p class="step-description">
                        Após escolher a melhor proposta, o desenvolvimento da solução 
                        será iniciado com acompanhamento contínuo.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Dica:</strong> Mantenha seu WhatsApp sempre disponível para receber 
                    as propostas dos talentos interessados em seu desafio.
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 