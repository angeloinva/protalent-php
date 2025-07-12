<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
require_once 'models/Talent.php';

$database = new Database();
$db = $database->getConnection();
$talent = new Talent($db);

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'create' || $action == 'edit') {
        $talent->name = $_POST['name'] ?? '';
        $talent->email = $_POST['email'] ?? '';
        $talent->phone = $_POST['phone'] ?? '';
        $talent->skills = $_POST['skills'] ?? '';
        $talent->experience_years = $_POST['experience_years'] ?? 0;
        $talent->salary_expectation = $_POST['salary_expectation'] ?? 0;
        $talent->status = $_POST['status'] ?? 'available';
        
        if ($action == 'create') {
            if ($talent->create()) {
                $message = 'Talento criado com sucesso!';
                $action = 'list';
            } else {
                $error = 'Erro ao criar talento!';
            }
        } else {
            $talent->id = $_POST['id'] ?? '';
            if ($talent->update()) {
                $message = 'Talento atualizado com sucesso!';
                $action = 'list';
            } else {
                $error = 'Erro ao atualizar talento!';
            }
        }
    } elseif ($action == 'delete') {
        $talent->id = $_POST['id'] ?? '';
        if ($talent->delete()) {
            $message = 'Talento excluído com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao excluir talento!';
        }
    }
}

include 'includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <!-- Lista de Talentos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">
                <i class="fas fa-user-tie me-2"></i>Gerenciar Talentos
            </h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="?action=create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Novo Talento
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="action" value="list">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="available" <?php echo ($_GET['status'] ?? '') == 'available' ? 'selected' : ''; ?>>Disponível</option>
                        <option value="hired" <?php echo ($_GET['status'] ?? '') == 'hired' ? 'selected' : ''; ?>>Contratado</option>
                        <option value="inactive" <?php echo ($_GET['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar por Habilidades</label>
                    <input type="text" name="skills" class="form-control" value="<?php echo $_GET['skills'] ?? ''; ?>" placeholder="PHP, JavaScript...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                        <a href="talents.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Talentos -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Habilidades</th>
                            <th>Experiência</th>
                            <th>Salário Esperado</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $talents = $talent->read();
                        while ($row = $talents->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars(substr($row['skills'], 0, 50)) . (strlen($row['skills']) > 50 ? '...' : ''); ?>
                                </small>
                            </td>
                            <td><?php echo $row['experience_years']; ?> anos</td>
                            <td>R$ <?php echo number_format($row['salary_expectation'], 2, ',', '.'); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['status'] == 'available' ? 'success' : 
                                        ($row['status'] == 'hired' ? 'danger' : 'secondary'); 
                                ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="?action=view&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?action=edit&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action == 'create' || $action == 'edit'): ?>
    <!-- Formulário de Criação/Edição -->
    <?php 
    if ($action == 'edit') {
        $talent->id = $_GET['id'] ?? '';
        $talent->readOne();
    }
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-<?php echo $action == 'create' ? 'plus' : 'edit'; ?> me-2"></i>
                <?php echo $action == 'create' ? 'Novo Talento' : 'Editar Talento'; ?>
            </h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $talent->id ?? ''; ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($talent->name ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($talent->email ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($talent->phone ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="experience_years" class="form-label">Anos de Experiência</label>
                        <input type="number" class="form-control" id="experience_years" name="experience_years" 
                               value="<?php echo $talent->experience_years ?? 0; ?>" min="0">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="skills" class="form-label">Habilidades</label>
                    <textarea class="form-control" id="skills" name="skills" rows="3" 
                              placeholder="Ex: PHP, MySQL, JavaScript, Laravel..."><?php echo htmlspecialchars($talent->skills ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="salary_expectation" class="form-label">Expectativa Salarial (R$)</label>
                        <input type="number" class="form-control" id="salary_expectation" name="salary_expectation" 
                               value="<?php echo $talent->salary_expectation ?? 0; ?>" min="0" step="0.01">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="available" <?php echo ($talent->status ?? '') == 'available' ? 'selected' : ''; ?>>Disponível</option>
                            <option value="hired" <?php echo ($talent->status ?? '') == 'hired' ? 'selected' : ''; ?>>Contratado</option>
                            <option value="inactive" <?php echo ($talent->status ?? '') == 'inactive' ? 'selected' : ''; ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="talents.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        <?php echo $action == 'create' ? 'Criar' : 'Atualizar'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php elseif ($action == 'view'): ?>
    <!-- Visualização de Talento -->
    <?php 
    $talent->id = $_GET['id'] ?? '';
    $talent->readOne();
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-user me-2"></i>Detalhes do Talento
            </h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informações Pessoais</h5>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($talent->name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($talent->email); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($talent->phone); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Informações Profissionais</h5>
                    <p><strong>Experiência:</strong> <?php echo $talent->experience_years; ?> anos</p>
                    <p><strong>Expectativa Salarial:</strong> R$ <?php echo number_format($talent->salary_expectation, 2, ',', '.'); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?php 
                            echo $talent->status == 'available' ? 'success' : 
                                ($talent->status == 'hired' ? 'danger' : 'secondary'); 
                        ?>">
                            <?php echo ucfirst($talent->status); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="mt-4">
                <h5>Habilidades</h5>
                <p><?php echo nl2br(htmlspecialchars($talent->skills)); ?></p>
            </div>
            
            <div class="mt-4">
                <a href="talents.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
                <a href="?action=edit&id=<?php echo $talent->id; ?>" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o talento <strong id="talentName"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="id" id="talentId">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('talentId').value = id;
    document.getElementById('talentName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?> 