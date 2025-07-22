<?php
session_start();
require_once 'config/database.php';

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

$database = new Database();
$db = $database->getConnection();

// Buscar desafios disponíveis
$desafios = [];
$stmt = $db->query('SELECT id, titulo FROM desafios WHERE status = "ativo"');
if ($stmt) {
    $desafios = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$desafio_id_url = isset($_GET['desafio_id']) ? intval($_GET['desafio_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_equipe = trim($_POST['nome_equipe']);
    $desafio_id = intval($_POST['desafio_id']);
    $alunos = $_POST['alunos']; // array de alunos
    $professor_id = $_SESSION['professor_id'];

    if (!$nome_equipe || !$desafio_id || empty($alunos)) {
        $error = 'Preencha todos os campos obrigatórios.';
    } else {
        // Inserir equipe
        $stmt = $db->prepare('INSERT INTO equipes (nome, professor_id, desafio_id) VALUES (?, ?, ?)');
        if ($stmt->execute([$nome_equipe, $professor_id, $desafio_id])) {
            $equipe_id = $db->lastInsertId();
            // Inserir alunos
            $ok = true;
            foreach ($alunos as $aluno) {
                $nome = trim($aluno['nome']);
                $email = trim($aluno['email']);
                $whatsapp = trim($aluno['whatsapp']);
                if ($nome && $email) {
                    $stmtAluno = $db->prepare('INSERT INTO alunos (nome, email, whatsapp, equipe_id) VALUES (?, ?, ?, ?)');
                    if (!$stmtAluno->execute([$nome, $email, $whatsapp, $equipe_id])) {
                        $ok = false;
                        break;
                    }
                }
            }
            if ($ok) {
                header('Location: professor-dashboard.php');
                exit();
            } else {
                $error = 'Erro ao cadastrar alunos.';
            }
        } else {
            $error = 'Erro ao cadastrar equipe.';
        }
    }
}

include 'includes/header.php';
?>
<div class="container mt-5">
    <h2>Cadastrar Equipe</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <form method="POST" id="formEquipe">
        <div class="mb-3">
            <label for="nome_equipe" class="form-label">Nome da Equipe *</label>
            <input type="text" class="form-control" id="nome_equipe" name="nome_equipe" required>
        </div>
        <div class="mb-3">
            <label for="desafio_id" class="form-label">Desafio *</label>
            <?php
            $desafio_escolhido = null;
            if ($desafio_id_url) {
                foreach ($desafios as $desafio) {
                    if ($desafio['id'] == $desafio_id_url) {
                        $desafio_escolhido = $desafio;
                        break;
                    }
                }
            }
            ?>
            <?php if ($desafio_escolhido): ?>
                <input type="text" class="form-control" value="<?= htmlspecialchars($desafio_escolhido['titulo']) ?>" readonly>
                <input type="hidden" name="desafio_id" value="<?= $desafio_escolhido['id'] ?>">
            <?php else: ?>
                <select class="form-control" id="desafio_id" name="desafio_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($desafios as $desafio): ?>
                        <option value="<?= $desafio['id'] ?>"><?= htmlspecialchars($desafio['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
        <h5>Alunos da Equipe</h5>
        <div id="alunos-lista">
            <div class="aluno-item row mb-2">
                <div class="col-md-4"><input type="text" name="alunos[0][nome]" class="form-control" placeholder="Nome do Aluno *" required></div>
                <div class="col-md-4"><input type="email" name="alunos[0][email]" class="form-control" placeholder="Email *" required></div>
                <div class="col-md-3"><input type="text" name="alunos[0][whatsapp]" class="form-control" placeholder="WhatsApp"></div>
                <div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-danger btn-sm remove-aluno" style="display:none">&times;</button></div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" id="addAluno">Adicionar Aluno</button>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Cadastrar Equipe</button>
        </div>
    </form>
</div>
<script>
let alunoIndex = 1;
document.getElementById('addAluno').addEventListener('click', function() {
    const lista = document.getElementById('alunos-lista');
    const item = document.createElement('div');
    item.className = 'aluno-item row mb-2';
    item.innerHTML = `
        <div class="col-md-4"><input type="text" name="alunos[${alunoIndex}][nome]" class="form-control" placeholder="Nome do Aluno *" required></div>
        <div class="col-md-4"><input type="email" name="alunos[${alunoIndex}][email]" class="form-control" placeholder="Email *" required></div>
        <div class="col-md-3"><input type="text" name="alunos[${alunoIndex}][whatsapp]" class="form-control whatsapp-field" placeholder="WhatsApp"></div>
        <div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-danger btn-sm remove-aluno">&times;</button></div>
    `;
    lista.appendChild(item);
    alunoIndex++;
    aplicarMascaraWhatsapp();
});
document.getElementById('alunos-lista').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-aluno')) {
        e.target.closest('.aluno-item').remove();
    }
    // Esconde botão de remover se só restar um aluno
    const total = document.querySelectorAll('.aluno-item').length;
    document.querySelectorAll('.remove-aluno').forEach(btn => {
        btn.style.display = total > 1 ? '' : 'none';
    });
});
// Máscara para WhatsApp nos campos dos alunos
function aplicarMascaraWhatsapp() {
    document.querySelectorAll('.whatsapp-field').forEach(function(input) {
        input.removeEventListener('input', mascaraHandler);
        input.addEventListener('input', mascaraHandler);
    });
}
function mascaraHandler(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    if (value.length > 0) value = '(' + value;
    if (value.length > 3) value = value.slice(0, 3) + ') ' + value.slice(3);
    if (value.length > 10) value = value.slice(0, 10) + '-' + value.slice(10);
    this.value = value;
}
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona classe para o campo inicial
    document.querySelectorAll('input[name^="alunos"][name$="[whatsapp]"]').forEach(function(input) {
        input.classList.add('whatsapp-field');
    });
    aplicarMascaraWhatsapp();
});
</script>
<?php include 'includes/footer.php'; ?> 