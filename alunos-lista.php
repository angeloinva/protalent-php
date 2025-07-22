<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$sql = 'SELECT a.nome AS aluno_nome, a.email AS aluno_email, a.whatsapp AS aluno_whatsapp, p.nome AS professor_nome, p.instituicao AS professor_instituicao, d.titulo AS desafio_titulo
        FROM alunos a
        JOIN equipes e ON a.equipe_id = e.id
        JOIN professores p ON e.professor_id = p.id
        JOIN desafios d ON e.desafio_id = d.id
        ORDER BY a.nome';
$stmt = $db->prepare($sql);
$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
include 'includes/header.php';
?>
<style>
.card-aluno {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    margin-bottom: 1.5rem;
    transition: box-shadow 0.2s;
}
.card-aluno:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.13);
}
.card-aluno .card-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: #2c3e50;
}
.card-aluno .badge {
    font-size: 0.95em;
}
</style>
<?php
// Exportação CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=alunos_cadastrados.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Nome', 'Email', 'WhatsApp', 'Professor', 'Instituição de Ensino', 'Desafio']);
    foreach ($alunos as $al) {
        fputcsv($output, [
            $al['aluno_nome'], $al['aluno_email'], $al['aluno_whatsapp'],
            $al['professor_nome'], $al['professor_instituicao'], $al['desafio_titulo']
        ]);
    }
    fclose($output);
    exit();
}
?>
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-users me-2"></i>Lista de Alunos Cadastrados</h2>
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <strong>Total de alunos cadastrados:</strong> <?= count($alunos) ?>
        </div>
        <div class="col text-end">
            <a href="alunos-lista.php?export=csv" class="btn btn-success"><i class="fas fa-file-csv me-2"></i>Exportar CSV</a>
        </div>
    </div>
    <div class="row">
        <?php foreach ($alunos as $al): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card card-aluno">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user fa-2x text-primary me-3"></i>
                        <div>
                            <div class="card-title mb-0"><?= htmlspecialchars($al['aluno_nome']) ?></div>
                            <div class="text-muted" style="font-size:0.95em;"> <?= htmlspecialchars($al['aluno_email']) ?> </div>
                        </div>
                    </div>
                    <div class="mb-2"><i class="fab fa-whatsapp me-1"></i><?= htmlspecialchars($al['aluno_whatsapp']) ?></div>
                    <div class="mb-2"><i class="fas fa-chalkboard-teacher me-1"></i><?= htmlspecialchars($al['professor_nome']) ?></div>
                    <div class="mb-2"><i class="fas fa-university me-1"></i><?= htmlspecialchars($al['professor_instituicao']) ?></div>
                    <div class="mb-2"><span class="badge bg-success"><i class="fas fa-trophy me-1"></i><?= htmlspecialchars($al['desafio_titulo']) ?></span></div>
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