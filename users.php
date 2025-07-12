<?php
session_start();

// Verificar se usuário está logado e é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'create' || $action == 'edit') {
        $user->name = $_POST['name'] ?? '';
        $user->email = $_POST['email'] ?? '';
        $user->role = $_POST['role'] ?? 'user';
        
        if ($action == 'create') {
            $user->password = $_POST['password'] ?? '';
            if ($user->create()) {
                $message = 'Usuário criado com sucesso!';
                $action = 'list';
            } else {
                $error = 'Erro ao criar usuário!';
            }
        } else {
            $user->id = $_POST['id'] ?? '';
            if ($user->update()) {
                $message = 'Usuário atualizado com sucesso!';
                $action = 'list';
            } else {
                $error = 'Erro ao atualizar usuário!';
            }
        }
    } elseif ($action == 'delete') {
        $user->id = $_POST['id'] ?? '';
        if ($user->delete()) {
            $message = 'Usuário excluído com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao excluir usuário!';
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
    <!-- Lista de Usuários -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">
                <i class="fas fa-users-cog me-2"></i>Gerenciar Usuários
            </h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="?action=create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Novo Usuário
            </a>
        </div>
    </div>

    <!-- Tabela de Usuários -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Função</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $users = $user->read();
                        while ($row = $users->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <a href="?action=edit&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
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
        $user->id = $_GET['id'] ?? '';
        $user->readOne();
    }
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-<?php echo $action == 'create' ? 'plus' : 'edit'; ?> me-2"></i>
                <?php echo $action == 'create' ? 'Novo Usuário' : 'Editar Usuário'; ?>
            </h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $user->id ?? ''; ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($user->name ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required>
                    </div>
                </div>
                
                <?php if ($action == 'create'): ?>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha *</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="role" class="form-label">Função</label>
                    <select class="form-select" id="role" name="role">
                        <option value="user" <?php echo ($user->role ?? '') == 'user' ? 'selected' : ''; ?>>Usuário</option>
                        <option value="admin" <?php echo ($user->role ?? '') == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="users.php" class="btn btn-secondary">
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
                <p>Tem certeza que deseja excluir o usuário <strong id="userName"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="id" id="userId">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('userId').value = id;
    document.getElementById('userName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?> 