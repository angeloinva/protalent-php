<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$sql = 'SELECT id, razao_social, nome_fantasia, cnpj, email, cidade FROM empresas ORDER BY nome_fantasia, razao_social';
$stmt = $db->prepare($sql);
$stmt->execute();
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
include 'includes/header.php';
?>
<style>
.card-empresa {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    margin-bottom: 1.5rem;
    transition: box-shadow 0.2s;
}
.card-empresa:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.13);
}
.card-empresa .card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
}
.card-empresa .card-subtitle {
    font-size: 0.95rem;
    color: #888;
}
.card-empresa .badge {
    font-size: 0.95em;
}
</style>
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-building me-2"></i>Empresas cadastradas</h2>
    <div class="row">
        <?php foreach ($empresas as $e): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card card-empresa">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-building fa-2x text-success me-3"></i>
                        <div>
                            <div class="card-title mb-0"><?= htmlspecialchars($e['nome_fantasia'] ?: $e['razao_social']) ?></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-id-card me-1"></i><?= htmlspecialchars($e['cnpj']) ?>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($e['email']) ?>
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-primary"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($e['cidade']) ?></span>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-3">
                        <a href="editar-empresa.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i> Editar</a>
                        <a href="excluir-empresa.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir esta empresa?')"><i class="fas fa-trash"></i> Excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-3">
        <a href="admin-dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard</a>
    </div>
</div>
<?php include 'includes/footer.php'; ?> 