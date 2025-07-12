<?php
session_start();

if (!isset($_SESSION['empresa_id'])) {
    header("Location: index.php");
    exit();
}

include 'includes/header.php';
?>

<style>
.welcome-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.welcome-content {
    background: white;
    border-radius: 15px;
    padding: 40px;
    margin-top: -40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    z-index: 10;
}

.welcome-icon {
    font-size: 4rem;
    color: #667eea;
    margin-bottom: 20px;
}

.welcome-title {
    color: #333;
    margin-bottom: 20px;
}

.welcome-message {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 30px;
}

.btn-welcome {
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

.btn-welcome:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-outline-welcome {
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

.btn-outline-welcome:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.features-section {
    padding: 60px 0;
    background: #f8f9fa;
}

.feature-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    height: 100%;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    font-size: 3rem;
    color: #667eea;
    margin-bottom: 20px;
}

.feature-title {
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.feature-description {
    color: #666;
    line-height: 1.6;
}
</style>

<div class="welcome-section">
    <div class="container">
        <div class="welcome-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="display-4 fw-bold">Bem-vindo ao ProTalent!</h1>
        <p class="lead">Sua empresa foi cadastrada com sucesso</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="welcome-content">
                <h2 class="welcome-title">Parabéns, <?php echo htmlspecialchars($_SESSION['empresa_nome']); ?>!</h2>
                <p class="welcome-message">
                    Sua empresa foi cadastrada com sucesso no ProTalent. Agora você pode começar a cadastrar desafios 
                    e conectar-se com talentos que podem ajudar a resolver seus problemas e impulsionar sua empresa.
                </p>
                
                <div class="text-center">
                    <a href="empresa-dashboard.php" class="btn btn-welcome">
                        <i class="fas fa-tachometer-alt me-2"></i>Acessar Dashboard
                    </a>
                    <a href="cadastrar-desafio.php" class="btn btn-outline-welcome">
                        <i class="fas fa-plus me-2"></i>Cadastrar Primeiro Desafio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold">O que você pode fazer agora?</h2>
                <p class="lead text-muted">Explore as funcionalidades disponíveis para sua empresa</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="feature-title">Cadastrar Desafios</h3>
                    <p class="feature-description">
                        Descreva os problemas que sua empresa enfrenta e deixe que talentos 
                        encontrem soluções inovadoras para você.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Conectar Talentos</h3>
                    <p class="feature-description">
                        Professores e alunos de universidades podem visualizar seus desafios 
                        e propor soluções criativas.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Acompanhar Progresso</h3>
                    <p class="feature-description">
                        Monitore o status dos seus desafios e acompanhe o desenvolvimento 
                        das soluções propostas.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 