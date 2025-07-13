<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id']) && !isset($_SESSION['empresa_id'])) {
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

$error = '';
$success = '';
$desafio_data = null;

// Verificar se ID do desafio foi fornecido
if (!isset($_GET['id'])) {
    header("Location: " . (isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'));
    exit();
}

$desafio->id = $_GET['id'];
$desafio_data = $desafio->readOne();

if (!$desafio_data) {
    header("Location: " . (isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'));
    exit();
}

// Verificar permissões
$is_admin = isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'admin';
$is_empresa_owner = isset($_SESSION['empresa_id']) && $desafio_data['empresa_id'] == $_SESSION['empresa_id'];

if (!$is_admin && !$is_empresa_owner) {
    header("Location: " . (isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'));
    exit();
}

// Buscar dados da empresa para preencher automaticamente
$empresa->id = $desafio_data['empresa_id'];
$empresa_data = $empresa->readOne();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $desafio->id = $_GET['id'];
    $desafio->empresa_id = $desafio_data['empresa_id']; // Manter a empresa original
    $desafio->mentor = $_POST['mentor'];
    $desafio->whatsapp = $_POST['whatsapp'];
    $desafio->titulo = $_POST['titulo'];
    $desafio->descricao_problema = $_POST['descricao_problema'];
    $desafio->pesquisado = isset($_POST['pesquisado']) ? 1 : 0;
    $desafio->descricao_pesquisa = $_POST['descricao_pesquisa'] ?? '';
    $desafio->nivel_trl = $_POST['nivel_trl'] ?? '';
    $desafio->requisitos_especificos = isset($_POST['requisitos_especificos']) ? 1 : 0;
    $desafio->descricao_requisitos = $_POST['descricao_requisitos'] ?? '';
    $desafio->status = $_POST['status'] ?? 'ativo';
    
    if ($desafio->update()) {
        $success = 'Desafio atualizado com sucesso!';
        // Recarregar dados do desafio
        $desafio_data = $desafio->readOne();
    } else {
        $error = 'Erro ao atualizar desafio!';
    }
}

include 'includes/header.php';
?>

<style>
.edicao-section {
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

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: border-color 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-editar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.btn-editar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.auto-fill-info {
    background: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 25px;
    color: #1976d2;
    font-weight: 500;
}

.conditional-field {
    display: none;
}

.conditional-field.show {
    display: block;
}
</style>

<div class="edicao-section">
    <div class="container">
        <h1 class="display-4 fw-bold">
            <i class="fas fa-edit me-3"></i>Editar Desafio
        </h1>
        <p class="lead">Atualize as informações do seu desafio</p>
    </div>
</div>

<div class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-card">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
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
                                           value="<?php echo htmlspecialchars($desafio_data['mentor']); ?>" required>
                                    <small class="form-text text-muted">Nome do responsável pelo projeto</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                                           value="<?php echo htmlspecialchars($desafio_data['whatsapp']); ?>" 
                                           placeholder="(11) 99999-9999">
                                    <small class="form-text text-muted">Contato para dúvidas sobre o projeto</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="titulo" class="form-label">Título do Desafio *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" 
                                   value="<?php echo htmlspecialchars($desafio_data['titulo']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao_problema" class="form-label">Descrição do Problema *</label>
                            <textarea class="form-control" id="descricao_problema" name="descricao_problema" 
                                      rows="5" required><?php echo htmlspecialchars($desafio_data['descricao_problema']); ?></textarea>
                            <small class="form-text text-muted">Descreva detalhadamente o problema que precisa ser resolvido</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pesquisado" name="pesquisado" 
                                               <?php echo $desafio_data['pesquisado'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="pesquisado">
                                            Já foi pesquisado anteriormente
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nivel_trl" class="form-label">Nível TRL</label>
                                    <select class="form-select" id="nivel_trl" name="nivel_trl">
                                        <option value="">Selecione o nível TRL</option>
                                        <option value="TRL 1" <?php echo $desafio_data['nivel_trl'] == 'TRL 1' ? 'selected' : ''; ?>>TRL 1 - Princípios básicos</option>
                                        <option value="TRL 2" <?php echo $desafio_data['nivel_trl'] == 'TRL 2' ? 'selected' : ''; ?>>TRL 2 - Conceito tecnológico</option>
                                        <option value="TRL 3" <?php echo $desafio_data['nivel_trl'] == 'TRL 3' ? 'selected' : ''; ?>>TRL 3 - Prova de conceito</option>
                                        <option value="TRL 4" <?php echo $desafio_data['nivel_trl'] == 'TRL 4' ? 'selected' : ''; ?>>TRL 4 - Validação em laboratório</option>
                                        <option value="TRL 5" <?php echo $desafio_data['nivel_trl'] == 'TRL 5' ? 'selected' : ''; ?>>TRL 5 - Validação em ambiente relevante</option>
                                        <option value="TRL 6" <?php echo $desafio_data['nivel_trl'] == 'TRL 6' ? 'selected' : ''; ?>>TRL 6 - Demonstração em ambiente relevante</option>
                                        <option value="TRL 7" <?php echo $desafio_data['nivel_trl'] == 'TRL 7' ? 'selected' : ''; ?>>TRL 7 - Protótipo em ambiente operacional</option>
                                        <option value="TRL 8" <?php echo $desafio_data['nivel_trl'] == 'TRL 8' ? 'selected' : ''; ?>>TRL 8 - Sistema completo qualificado</option>
                                        <option value="TRL 9" <?php echo $desafio_data['nivel_trl'] == 'TRL 9' ? 'selected' : ''; ?>>TRL 9 - Sistema real comprovado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="conditional-field" id="descricao_pesquisa_field">
                            <div class="form-group">
                                <label for="descricao_pesquisa" class="form-label">Descrição da Pesquisa Anterior</label>
                                <textarea class="form-control" id="descricao_pesquisa" name="descricao_pesquisa" 
                                          rows="3"><?php echo htmlspecialchars($desafio_data['descricao_pesquisa']); ?></textarea>
                                <small class="form-text text-muted">Descreva o que já foi pesquisado sobre este problema</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="requisitos_especificos" name="requisitos_especificos" 
                                               <?php echo $desafio_data['requisitos_especificos'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="requisitos_especificos">
                                            Possui requisitos específicos
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status do Desafio</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="ativo" <?php echo $desafio_data['status'] == 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="em_andamento" <?php echo $desafio_data['status'] == 'em_andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                        <option value="concluido" <?php echo $desafio_data['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="conditional-field" id="descricao_requisitos_field">
                            <div class="form-group">
                                <label for="descricao_requisitos" class="form-label">Descrição dos Requisitos Específicos</label>
                                <textarea class="form-control" id="descricao_requisitos" name="descricao_requisitos" 
                                          rows="3"><?php echo htmlspecialchars($desafio_data['descricao_requisitos']); ?></textarea>
                                <small class="form-text text-muted">Descreva os requisitos específicos para a solução</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo isset($_SESSION['user_id']) ? 'admin-dashboard.php' : 'empresa-dashboard.php'; ?>" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-editar">
                                <i class="fas fa-save me-2"></i>Atualizar Desafio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pesquisadoCheckbox = document.getElementById('pesquisado');
    const descricaoPesquisaField = document.getElementById('descricao_pesquisa_field');
    const requisitosCheckbox = document.getElementById('requisitos_especificos');
    const descricaoRequisitosField = document.getElementById('descricao_requisitos_field');
    
    // Função para mostrar/ocultar campos condicionais
    function toggleConditionalFields() {
        if (pesquisadoCheckbox.checked) {
            descricaoPesquisaField.classList.add('show');
        } else {
            descricaoPesquisaField.classList.remove('show');
        }
        
        if (requisitosCheckbox.checked) {
            descricaoRequisitosField.classList.add('show');
        } else {
            descricaoRequisitosField.classList.remove('show');
        }
    }
    
    // Executar na carga da página
    toggleConditionalFields();
    
    // Adicionar event listeners
    pesquisadoCheckbox.addEventListener('change', toggleConditionalFields);
    requisitosCheckbox.addEventListener('change', toggleConditionalFields);
});
</script>

<?php include 'includes/footer.php'; ?> 