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

// Buscar dados da empresa para preencher automaticamente
$empresa->id = $_SESSION['empresa_id'];
$empresa_data = $empresa->readOne();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $desafio->empresa_id = $_SESSION['empresa_id'];
    $desafio->mentor = $_POST['mentor'];
    $desafio->whatsapp = $_POST['whatsapp'];
    $desafio->titulo = $_POST['titulo'];
    $desafio->descricao_problema = $_POST['descricao_problema'];
    $desafio->pesquisado = isset($_POST['pesquisado']) ? 1 : 0;
    $desafio->requisitos_especificos = isset($_POST['requisitos_especificos']) ? 1 : 0;
    $desafio->descricao_requisitos = $_POST['descricao_requisitos'] ?? '';
    $desafio->status = 'ativo';
    
    if ($desafio->create()) {
        header("Location: desafio-cadastrado.php");
        exit();
    } else {
        $error = 'Erro ao cadastrar desafio!';
    }
}

include 'includes/header.php';
?>

<style>
.cadastro-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    text-align: center;
}

.form-section {
    padding: 40px 0;
}

.form-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.form-title {
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 8px;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.btn-cadastrar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.btn-cadastrar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.auto-fill-info {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    color: #1976d2;
}

.requisitos-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
    border-left: 4px solid #667eea;
}

.requisitos-fields {
    display: none;
    margin-top: 15px;
}

.checkbox-group {
    margin-bottom: 15px;
}

.checkbox-group input[type="checkbox"] {
    margin-right: 10px;
}

.checkbox-group label {
    font-weight: 500;
    color: #555;
    cursor: pointer;
}
</style>

<div class="cadastro-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Cadastrar Novo Desafio</h1>
                <p class="lead">Descreva o problema que sua empresa enfrenta e conecte-se com talentos</p>
            </div>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-card">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" id="desafioForm">
                        <h3 class="form-title">Dados do Desafio</h3>
                        
                        <div class="auto-fill-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Empresa:</strong> <?php echo htmlspecialchars($empresa_data['nome_fantasia'] ?: $empresa_data['razao_social']); ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mentor" class="form-label">Mentor *</label>
                                    <input type="text" class="form-control" id="mentor" name="mentor" 
                                           value="<?php echo htmlspecialchars($empresa_data['nome_mentor']); ?>" required>
                                    <small class="form-text text-muted">Nome do responsável pelo projeto</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                                           value="<?php echo htmlspecialchars($empresa_data['whatsapp']); ?>" 
                                           placeholder="(11) 99999-9999">
                                    <small class="form-text text-muted">Contato para dúvidas sobre o projeto</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="titulo" class="form-label">Título do Desafio *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" 
                                   placeholder="Ex: Sistema de gestão de estoque automatizado" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao_problema" class="form-label">Descrição do Problema *</label>
                            <textarea class="form-control form-textarea" id="descricao_problema" name="descricao_problema" 
                                      placeholder="Descreva a situação/problema: como é realizado atualmente e qual o problema enfrentado" required></textarea>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="pesquisado" name="pesquisado">
                            <label for="pesquisado">
                                Você já pesquisou ou iniciou o desenvolvimento de alguma solução para este problema?
                            </label>
                        </div>
                        
                        <div class="requisitos-section">
                            <div class="checkbox-group">
                                <input type="checkbox" id="requisitos_especificos" name="requisitos_especificos">
                                <label for="requisitos_especificos">
                                    Existe algum requisito que você faz muita questão que esteja contemplado no desenvolvimento dessa solução?
                                </label>
                            </div>
                            
                            <div class="requisitos-fields" id="requisitosFields">
                                <div class="form-group">
                                    <label for="descricao_requisitos" class="form-label">Qual? *</label>
                                    <textarea class="form-control form-textarea" id="descricao_requisitos" name="descricao_requisitos" 
                                              placeholder="Descreva os requisitos específicos que devem ser atendidos"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-cadastrar">
                                <i class="fas fa-check me-2"></i>Cadastrar Desafio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Máscara para WhatsApp
document.getElementById('whatsapp').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    e.target.value = value;
});

// Mostrar/ocultar campos de requisitos
document.getElementById('requisitos_especificos').addEventListener('change', function(e) {
    const requisitosFields = document.getElementById('requisitosFields');
    const descricaoRequisitos = document.getElementById('descricao_requisitos');
    
    if (e.target.checked) {
        requisitosFields.style.display = 'block';
        descricaoRequisitos.required = true;
    } else {
        requisitosFields.style.display = 'none';
        descricaoRequisitos.required = false;
        descricaoRequisitos.value = '';
    }
});

// Validação do formulário
document.getElementById('desafioForm').addEventListener('submit', function(e) {
    const requisitosEspecificos = document.getElementById('requisitos_especificos');
    const descricaoRequisitos = document.getElementById('descricao_requisitos');
    
    if (requisitosEspecificos.checked && !descricaoRequisitos.value.trim()) {
        e.preventDefault();
        alert('Por favor, descreva os requisitos específicos.');
        descricaoRequisitos.focus();
        return false;
    }
});
</script>

<?php include 'includes/footer.php'; ?> 