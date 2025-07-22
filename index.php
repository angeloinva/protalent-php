<?php
session_start();

// Se já estiver logado, redirecionar para a área apropriada
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] == 'empresa') {
        header("Location: empresa-dashboard.php");
        exit();
    } elseif ($_SESSION['user_type'] == 'professor') {
        header("Location: professor-dashboard.php");
        exit();
    }
}

include 'includes/header.php';
?>

<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.logo-section {
    margin-bottom: 30px;
}

.logo-section img {
    max-width: 200px;
    height: auto;
    margin-bottom: 20px;
}

.slogan {
    font-size: 1.5rem;
    font-weight: 300;
    margin-bottom: 50px;
    opacity: 0.9;
}

.user-type-cards {
    margin-top: 50px;
}

.user-type-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    border: 2px solid transparent;
}

.user-type-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.user-type-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.user-type-card.disabled:hover {
    transform: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.user-type-icon {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #667eea;
}

.user-type-card.disabled .user-type-icon {
    color: #ccc;
}

.user-type-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.user-type-description {
    color: #666;
    margin-bottom: 25px;
    line-height: 1.6;
}

.btn-user-type {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-user-type:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-user-type.disabled {
    background: #ccc;
    cursor: not-allowed;
}

.btn-user-type.disabled:hover {
    transform: none;
    box-shadow: none;
}

.coming-soon-badge {
    background: #ff6b6b;
    color: white;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    margin-top: 10px;
    display: inline-block;
}
</style>

<div class="hero-section">
    <div class="container">
        <div >
            <img src="imgs/logo_protalent_branco.png" alt="ProTalent" onerror="this.style.display='none'" width="80%">
            
        </div>
        
        
        
        <div class="row user-type-cards justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="user-type-card">
                    <div class="user-type-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="user-type-title">Empresa</h3>
                    <p class="user-type-description">
                        Cadastre seus desafios e conecte-se com talentos para encontrar soluções inovadoras.
                    </p>
                    <a href="login-empresa.php" class="btn btn-user-type">
                        <i class="fas fa-arrow-right me-2"></i>Acessar
                    </a>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="user-type-card">
                    <div class="user-type-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="user-type-title">Professor</h3>
                    <p class="user-type-description">
                        Explore desafios reais das empresas e participe de projetos inovadores com seus alunos.
                    </p>
                    <a href="desafios-professor.php" class="btn btn-user-type">
                        <i class="fas fa-arrow-right me-2"></i>Acessar
                    </a>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="user-type-card disabled">
                    <div class="user-type-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3 class="user-type-title">Prestador de Serviço</h3>
                    <p class="user-type-description">
                        Conecte-se diretamente com empresas que precisam de soluções.
                    </p>
                    <button class="btn btn-user-type disabled" disabled>
                        <i class="fas fa-clock me-2"></i>Em breve
                    </button>
                    <div class="coming-soon-badge">
                        Em desenvolvimento
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 