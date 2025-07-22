<?php
session_start();
require_once 'config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$success = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $whatsapp = trim($_POST['whatsapp']);
    $instituicao = trim($_POST['instituicao']);
    $area_atuacao = trim($_POST['area_atuacao']);

    if (!$nome || !$email || !$senha) {
        $error = 'Preencha todos os campos obrigatórios.';
    } elseif ($senha !== $confirmar_senha) {
        $error = 'As senhas não coincidem.';
    } else {
        // Verifica se já existe
        $stmt = $db->prepare('SELECT id FROM professores WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Já existe um professor cadastrado com este email!';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO professores (nome, email, whatsapp, instituicao, area_atuacao, password) VALUES (?, ?, ?, ?, ?, ?)');
            if ($stmt->execute([$nome, $email, $whatsapp, $instituicao, $area_atuacao, $hash])) {
                // Enviar e-mail de boas-vindas
                $assunto = 'Bem-vindo(a) ao ProTalent!';
                $mensagem = "<h2>Olá, $nome!</h2>\n<p>Seu cadastro como professor foi realizado com sucesso no ProTalent! Agora você pode acessar a plataforma e participar dos desafios com seus alunos.</p>\n<p>Estamos muito felizes em tê-lo(a) conosco. Acesse a plataforma e confira os desafios disponíveis para você e seus alunos!</p>\n<p>Qualquer dúvida, estamos à disposição.</p>\n<p><b>Equipe ProTalent</b></p>";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: ProTalent <nao-responda@protalent.com>' . "\r\n";
                @mail($email, $assunto, $mensagem, $headers);
                // Login automático
                $professor_id = $db->lastInsertId();
                $_SESSION['professor_id'] = $professor_id;
                $_SESSION['professor_nome'] = $nome;
                $_SESSION['user_type'] = 'professor';
                if ($redirect) {
                    header('Location: ' . $redirect);
                } else {
                    header('Location: professor-dashboard.php');
                }
                exit();
            } else {
                $error = 'Erro ao cadastrar professor.';
            }
        }
    }
}

include 'includes/header.php';
?>
<div class="container mt-5">
    <h2>Cadastrar Professor</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" autocomplete="off">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome *</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" class="form-control" id="email" name="email" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="whatsapp" class="form-label">WhatsApp</label>
            <input type="text" class="form-control" id="whatsapp" name="whatsapp" maxlength="15" autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="instituicao" class="form-label">Instituição de ensino</label>
            <input type="text" class="form-control" id="instituicao" name="instituicao">
        </div>
        <div class="mb-3">
            <label for="area_atuacao" class="form-label">Área de Atuação</label>
            <select class="form-control" id="area_atuacao" name="area_atuacao" required>
                <option value="">Selecione...</option>
                <option value="Engenharias">Engenharias</option>
                <option value="Tecnologia da Informação / Software">Tecnologia da Informação / Software</option>
                <option value="Eletrônica / Automação">Eletrônica / Automação</option>
                <option value="Design e Inovação">Design e Inovação</option>
                <option value="Gestão e Negócios">Gestão e Negócios</option>
                <option value="Ciências Exatas e Naturais (Física, Química, Biotec)">Ciências Exatas e Naturais (Física, Química, Biotec)</option>
                <option value="Educação e Metodologias">Educação e Metodologias</option>
                <option value="Outros">Outros (especificar)</option>
            </select>
            <input type="text" class="form-control mt-2" id="area_atuacao_outros" name="area_atuacao_outros" placeholder="Especifique a área" style="display:none;">
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha *</label>
            <input type="password" class="form-control" id="senha" name="senha" required minlength="6" autocomplete="new-password">
        </div>
        <div class="mb-3">
            <label for="confirmar_senha" class="form-label">Confirmar Senha *</label>
            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required minlength="6" autocomplete="new-password">
        </div>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>
<script>
// Máscara para WhatsApp (formato brasileiro)
document.addEventListener('DOMContentLoaded', function() {
    var whatsappInput = document.getElementById('whatsapp');
    whatsappInput.addEventListener('input', function(e) {
        let value = whatsappInput.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        if (value.length > 0) value = '(' + value;
        if (value.length > 3) value = value.slice(0, 3) + ') ' + value.slice(3);
        if (value.length > 10) value = value.slice(0, 10) + '-' + value.slice(10);
        whatsappInput.value = value;
    });
});
// Validação de senha igual
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    const senha = document.getElementById('senha').value;
    const confirmar = document.getElementById('confirmar_senha').value;
    if (senha !== confirmar) {
        e.preventDefault();
        alert('As senhas não coincidem!');
    }
});
// Exibir campo de texto se "Outros" for selecionado
const areaSelect = document.getElementById('area_atuacao');
const outrosInput = document.getElementById('area_atuacao_outros');
areaSelect.addEventListener('change', function() {
    if (areaSelect.value === 'Outros') {
        outrosInput.style.display = 'block';
        outrosInput.required = true;
    } else {
        outrosInput.style.display = 'none';
        outrosInput.required = false;
    }
});
// Ajustar valor do campo para submissão
const form2 = document.querySelector('form');
form2.addEventListener('submit', function(e) {
    if (areaSelect.value === 'Outros') {
        areaSelect.value = outrosInput.value;
    }
});
</script>
<?php include 'includes/footer.php'; ?> 