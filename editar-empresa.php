<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header('Location: empresas-lista.php');
    exit();
}
// Buscar empresa
$stmt = $db->prepare('SELECT * FROM empresas WHERE id = ?');
$stmt->execute([$id]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$empresa) {
    header('Location: empresas-lista.php');
    exit();
}
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_fantasia = trim($_POST['nome_fantasia']);
    $razao_social = trim($_POST['razao_social']);
    $cnpj = trim($_POST['cnpj']);
    $email = trim($_POST['email']);
    $cidade = trim($_POST['cidade']);
    if ($nome_fantasia && $razao_social && $cnpj && $email) {
        $stmt = $db->prepare('UPDATE empresas SET nome_fantasia = ?, razao_social = ?, cnpj = ?, email = ?, cidade = ? WHERE id = ?');
        if ($stmt->execute([$nome_fantasia, $razao_social, $cnpj, $email, $cidade, $id])) {
            $success = 'Empresa atualizada com sucesso!';
            // Atualizar dados exibidos
            $empresa['nome_fantasia'] = $nome_fantasia;
            $empresa['razao_social'] = $razao_social;
            $empresa['cnpj'] = $cnpj;
            $empresa['email'] = $email;
            $empresa['cidade'] = $cidade;
        } else {
            $error = 'Erro ao atualizar empresa.';
        }
    } else {
        $error = 'Preencha todos os campos obrigatórios.';
    }
}
include 'includes/header.php';
?>
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-edit me-2"></i>Editar Empresa</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success"> <?= $success ?> </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"> <?= $error ?> </div>
            <?php endif; ?>
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome Fantasia *</label>
                        <input type="text" name="nome_fantasia" class="form-control" value="<?= htmlspecialchars($empresa['nome_fantasia']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Razão Social *</label>
                        <input type="text" name="razao_social" class="form-control" value="<?= htmlspecialchars($empresa['razao_social']) ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">CNPJ *</label>
                        <input type="text" name="cnpj" class="form-control" value="<?= htmlspecialchars($empresa['cnpj']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($empresa['email']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="cidade" class="form-control" value="<?= htmlspecialchars($empresa['cidade']) ?>">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="empresas-lista.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?> 