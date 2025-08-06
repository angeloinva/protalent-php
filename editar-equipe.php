<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$equipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$equipe_id) {
    header('Location: index.php');
    exit();
}
$stmtEquipe = $db->prepare('SELECT equipes.*, professores.nome AS professor_nome FROM equipes JOIN professores ON equipes.professor_id = professores.id WHERE equipes.id = ?');
$stmtEquipe->execute([$equipe_id]);
$equipe = $stmtEquipe->fetch(PDO::FETCH_ASSOC);
if (!$equipe) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Equipe não encontrada.</div></div>';
    exit();
}

$success = '';
$error = '';

// Buscar alunos da equipe
$stmtAlunos = $db->prepare('SELECT * FROM alunos WHERE equipe_id = ? ORDER BY nome');
$stmtAlunos->execute([$equipe_id]);
$alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'editar_equipe':
                $novo_nome = trim($_POST['nome']);
                if ($novo_nome) {
                    $stmtUpdate = $db->prepare('UPDATE equipes SET nome = ? WHERE id = ?');
                    if ($stmtUpdate->execute([$novo_nome, $equipe_id])) {
                        $success = 'Nome da equipe atualizado com sucesso!';
                        $equipe['nome'] = $novo_nome;
                    }
                }
                break;
                
            case 'adicionar_aluno':
                $nome = trim($_POST['nome_aluno']);
                $email = trim($_POST['email_aluno']);
                $whatsapp = trim($_POST['whatsapp_aluno']);
                
                if ($nome && $email) {
                    $stmtInsert = $db->prepare('INSERT INTO alunos (nome, email, whatsapp, equipe_id) VALUES (?, ?, ?, ?)');
                    if ($stmtInsert->execute([$nome, $email, $whatsapp, $equipe_id])) {
                        $success = 'Aluno adicionado com sucesso!';
                        // Recarregar lista de alunos
                        $stmtAlunos->execute([$equipe_id]);
                        $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $error = 'Erro ao adicionar aluno.';
                    }
                } else {
                    $error = 'Nome e email são obrigatórios.';
                }
                break;
                
            case 'editar_aluno':
                $aluno_id = intval($_POST['aluno_id']);
                $nome = trim($_POST['nome_aluno']);
                $email = trim($_POST['email_aluno']);
                $whatsapp = trim($_POST['whatsapp_aluno']);
                
                if ($aluno_id && $nome && $email) {
                    $stmtUpdate = $db->prepare('UPDATE alunos SET nome = ?, email = ?, whatsapp = ? WHERE id = ? AND equipe_id = ?');
                    if ($stmtUpdate->execute([$nome, $email, $whatsapp, $aluno_id, $equipe_id])) {
                        $success = 'Aluno atualizado com sucesso!';
                        // Recarregar lista de alunos
                        $stmtAlunos->execute([$equipe_id]);
                        $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $error = 'Erro ao atualizar aluno.';
                    }
                } else {
                    $error = 'Nome e email são obrigatórios.';
                }
                break;
                
            case 'excluir_aluno':
                $aluno_id = intval($_POST['aluno_id']);
                if ($aluno_id) {
                    $stmtDelete = $db->prepare('DELETE FROM alunos WHERE id = ? AND equipe_id = ?');
                    if ($stmtDelete->execute([$aluno_id, $equipe_id])) {
                        $success = 'Aluno removido com sucesso!';
                        // Recarregar lista de alunos
                        $stmtAlunos->execute([$equipe_id]);
                        $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $error = 'Erro ao remover aluno.';
                    }
                }
                break;
        }
    }
}

include 'includes/header.php';
?>

<style>
.edicao-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    margin-bottom: 30px;
}

.edicao-content {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.edicao-title {
    color: #333;
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.form-section {
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

.btn-cancelar {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-cancelar:hover {
    background: #667eea;
    color: white;
    text-decoration: none;
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

.info-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #667eea;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.info-item i {
    margin-right: 10px;
    color: #667eea;
    width: 20px;
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.aluno-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    transition: transform 0.2s ease;
}

.aluno-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.aluno-info {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.aluno-info i {
    margin-right: 10px;
    color: #667eea;
    width: 20px;
}

.btn-aluno {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-right: 5px;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
</style>

<div class="edicao-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">
                    <i class="fas fa-edit me-3"></i>Editar Equipe
                </h1>
                <p class="lead mb-0">Modifique as informações da equipe e seus membros</p>
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
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="edicao-content">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <h2 class="edicao-title">
                    <i class="fas fa-users me-2"></i><?= htmlspecialchars($equipe['nome']) ?>
                </h2>
                
                <!-- Informações da Equipe -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>Informações da Equipe
                    </h3>
                    <div class="info-card">
                        <div class="info-item">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span><strong>Professor Responsável:</strong> <?= htmlspecialchars($equipe['professor_nome']) ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Data de Criação:</strong> <?= date('d/m/Y', strtotime($equipe['created_at'])) ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span><strong>Há:</strong> <?= date_diff(date_create($equipe['created_at']), date_create())->days ?> dias</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-user-graduate"></i>
                            <span><strong>Total de Alunos:</strong> <?= count($alunos) ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Editar Nome da Equipe -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-edit me-2"></i>Editar Nome da Equipe
                    </h3>
                    <form method="POST">
                        <input type="hidden" name="acao" value="editar_equipe">
                        <div class="mb-4">
                            <label for="nome" class="form-label">
                                <i class="fas fa-tag me-2"></i>Nome da Equipe
                            </label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?= htmlspecialchars($equipe['nome']) ?>" required
                                   placeholder="Digite o novo nome da equipe">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                O nome da equipe será atualizado para todos os membros.
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-editar">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Gerenciar Alunos -->
                <div class="form-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="section-title mb-0">
                            <i class="fas fa-user-graduate me-2"></i>Alunos da Equipe
                        </h3>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionarAlunoModal">
                            <i class="fas fa-plus me-2"></i>Adicionar Aluno
                        </button>
                    </div>
                    
                    <?php if (count($alunos) === 0): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhum aluno cadastrado nesta equipe. Clique em "Adicionar Aluno" para começar.
                        </div>
                    <?php else: ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <div class="aluno-card">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="aluno-info">
                                            <i class="fas fa-user"></i>
                                            <strong><?= htmlspecialchars($aluno['nome']) ?></strong>
                                        </div>
                                        <div class="aluno-info">
                                            <i class="fas fa-envelope"></i>
                                            <span><?= htmlspecialchars($aluno['email']) ?></span>
                                        </div>
                                        <?php if ($aluno['whatsapp']): ?>
                                        <div class="aluno-info">
                                            <i class="fab fa-whatsapp"></i>
                                            <span><?= htmlspecialchars($aluno['whatsapp']) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button type="button" class="btn btn-warning btn-aluno" 
                                                onclick="editarAluno(<?= $aluno['id'] ?>, '<?= htmlspecialchars($aluno['nome']) ?>', '<?= htmlspecialchars($aluno['email']) ?>', '<?= htmlspecialchars($aluno['whatsapp']) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-aluno" 
                                                onclick="excluirAluno(<?= $aluno['id'] ?>, '<?= htmlspecialchars($aluno['nome']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Aluno -->
<div class="modal fade" id="adicionarAlunoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Adicionar Aluno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="adicionar_aluno">
                    <div class="mb-3">
                        <label for="nome_aluno" class="form-label">Nome do Aluno *</label>
                        <input type="text" class="form-control" id="nome_aluno" name="nome_aluno" required>
                    </div>
                    <div class="mb-3">
                        <label for="email_aluno" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email_aluno" name="email_aluno" required>
                    </div>
                    <div class="mb-3">
                        <label for="whatsapp_aluno" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp_aluno" name="whatsapp_aluno" placeholder="(11) 99999-9999">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Adicionar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Aluno -->
<div class="modal fade" id="editarAlunoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Aluno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="editar_aluno">
                    <input type="hidden" name="aluno_id" id="edit_aluno_id">
                    <div class="mb-3">
                        <label for="edit_nome_aluno" class="form-label">Nome do Aluno *</label>
                        <input type="text" class="form-control" id="edit_nome_aluno" name="nome_aluno" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email_aluno" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="edit_email_aluno" name="email_aluno" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_whatsapp_aluno" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="edit_whatsapp_aluno" name="whatsapp_aluno" placeholder="(11) 99999-9999">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulário oculto para exclusão -->
<form id="formExcluirAluno" method="POST" style="display: none;">
    <input type="hidden" name="acao" value="excluir_aluno">
    <input type="hidden" name="aluno_id" id="excluir_aluno_id">
</form>

<script>
function editarAluno(id, nome, email, whatsapp) {
    document.getElementById('edit_aluno_id').value = id;
    document.getElementById('edit_nome_aluno').value = nome;
    document.getElementById('edit_email_aluno').value = email;
    document.getElementById('edit_whatsapp_aluno').value = whatsapp;
    new bootstrap.Modal(document.getElementById('editarAlunoModal')).show();
}

function excluirAluno(id, nome) {
    if (confirm('Tem certeza que deseja excluir o aluno "' + nome + '"? Esta ação não pode ser desfeita.')) {
        document.getElementById('excluir_aluno_id').value = id;
        document.getElementById('formExcluirAluno').submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>