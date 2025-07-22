<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

$professor_id = $_SESSION['professor_id'];
$equipe_id = isset($_GET['equipe_id']) ? intval($_GET['equipe_id']) : 0;
$desafio_id = isset($_GET['desafio_id']) ? intval($_GET['desafio_id']) : 0;

if (!$equipe_id || !$desafio_id) {
    echo 'Parâmetros inválidos.';
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Bloco de edição do diário de bordo (apenas aqui)
if (isset($_POST['acao']) && $_POST['acao'] === 'editar_diario') {
    $id_diario = intval($_POST['id_diario']);
    $titulo_edit = trim($_POST['titulo_diario_edit']);
    $desc_edit = trim($_POST['descricao_diario_edit']);
    if ($id_diario && $titulo_edit && $desc_edit) {
        $stmt = $db->prepare('UPDATE diario_bordo SET titulo = ?, descricao = ? WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
        $stmt->execute([$titulo_edit, $desc_edit, $id_diario, $equipe_id, $desafio_id]);
        header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-diario');
        exit();
    }
}

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header('Location: login.php');
    exit();
}

// Verifica se a equipe pertence ao professor
$stmt = $db->prepare('SELECT equipes.nome AS equipe_nome, desafios.titulo AS desafio_titulo FROM equipes JOIN desafios ON equipes.desafio_id = desafios.id WHERE equipes.id = ? AND equipes.professor_id = ?');
$stmt->execute([$equipe_id, $professor_id]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$info) {
    echo 'Acesso não autorizado.';
    exit();
}

$error = '';
$success = '';

// Buscar alunos da equipe
$alunos = [];
$stmt = $db->prepare('SELECT id, nome, email, whatsapp FROM alunos WHERE equipe_id = ? ORDER BY nome');
$stmt->execute([$equipe_id]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && $_POST['acao'] === 'diario') {
        $titulo_diario = trim($_POST['titulo_diario']);
        $descricao_diario = trim($_POST['descricao_diario']);
        if (!$titulo_diario || !$descricao_diario) {
            $error = 'Preencha todos os campos do diário de bordo.';
        } else {
            $stmt = $db->prepare('INSERT INTO diario_bordo (equipe_id, desafio_id, titulo, descricao) VALUES (?, ?, ?, ?)');
            if ($stmt->execute([$equipe_id, $desafio_id, $titulo_diario, $descricao_diario])) {
                $success = 'Registro do diário de bordo salvo com sucesso!';
            } else {
                $error = 'Erro ao salvar registro do diário de bordo.';
            }
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'tarefa') {
        $titulo_tarefa = trim($_POST['titulo_tarefa']);
        $status_tarefa = in_array($_POST['status'] ?? '', ['afazer','andamento','concluido']) ? $_POST['status'] : 'afazer';
        if ($titulo_tarefa) {
            $stmt = $db->prepare('INSERT INTO kanban_tarefas (equipe_id, desafio_id, titulo, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$equipe_id, $desafio_id, $titulo_tarefa, $status_tarefa]);
            header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-tarefas');
            exit();
        } else {
            $error = 'Digite o título da tarefa.';
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'mover_tarefa') {
        $tarefa_id = intval($_POST['tarefa_id']);
        $novo_status = $_POST['novo_status'];
        if (in_array($novo_status, ['afazer', 'andamento', 'concluido'])) {
            $stmt = $db->prepare('UPDATE kanban_tarefas SET status = ? WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
            $stmt->execute([$novo_status, $tarefa_id, $equipe_id, $desafio_id]);
            $success = 'Tarefa movida!';
        }
    } else {
        $status = trim($_POST['status'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        if ($status || $descricao) {
            if (!$status || !$descricao) {
                $error = 'Preencha todos os campos.';
            } else {
                $stmt = $db->prepare('INSERT INTO andamento_solucoes (equipe_id, desafio_id, status, descricao) VALUES (?, ?, ?, ?)');
                if ($stmt->execute([$equipe_id, $desafio_id, $status, $descricao])) {
                    $success = 'Andamento registrado com sucesso!';
                } else {
                    $error = 'Erro ao registrar andamento.';
                }
            }
        }
    }
    if (isset($_POST['acao']) && $_POST['acao'] === 'aluno') {
        $nome_aluno = trim($_POST['nome_aluno']);
        $email_aluno = trim($_POST['email_aluno']);
        $whatsapp_aluno = trim($_POST['whatsapp_aluno']);
        $id_aluno = isset($_POST['id_aluno']) ? intval($_POST['id_aluno']) : 0;
        if ($id_aluno > 0) {
            // Editar aluno
            $stmt = $db->prepare('UPDATE alunos SET nome = ?, email = ?, whatsapp = ? WHERE id = ? AND equipe_id = ?');
            $stmt->execute([$nome_aluno, $email_aluno, $whatsapp_aluno, $id_aluno, $equipe_id]);
            // Redirecionar para a área de alunos
            header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-alunos');
            exit();
        } else {
            // Adicionar novo aluno
            if ($nome_aluno && $email_aluno) {
                $stmt = $db->prepare('INSERT INTO alunos (nome, email, whatsapp, equipe_id) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nome_aluno, $email_aluno, $whatsapp_aluno, $equipe_id]);
                // Redirecionar para a área de alunos
                header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-alunos');
                exit();
            } else {
                $error = 'Preencha nome e email do aluno.';
            }
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'excluir_aluno') {
        $id_aluno = intval($_POST['id_aluno']);
        $stmt = $db->prepare('DELETE FROM alunos WHERE id = ? AND equipe_id = ?');
        $stmt->execute([$id_aluno, $equipe_id]);
        $success = 'Aluno removido!';
    }
    if (isset($_POST['acao']) && $_POST['acao'] === 'excluir_diario') {
        $id_diario = intval($_POST['id_diario']);
        if ($id_diario) {
            $stmt = $db->prepare('DELETE FROM diario_bordo WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
            $stmt->execute([$id_diario, $equipe_id, $desafio_id]);
        }
    }
}

// --- Solução parcial: salvar, buscar, editar e excluir ---
if (isset($_POST['acao']) && $_POST['acao'] === 'parcial') {
    $desc_parcial = trim($_POST['descricao_parcial']);
    $id_parcial = isset($_POST['id_parcial']) ? intval($_POST['id_parcial']) : 0;
    $url_video = trim($_POST['url_video'] ?? '');
    $arquivo_nome = '';
    if (!empty($_FILES['arquivo_parcial']['name'])) {
        if ($_FILES['arquivo_parcial']['size'] > 16 * 1024 * 1024) {
            $error = 'O arquivo deve ter no máximo 16MB.';
        } else {
            $arquivo_nome = date('YmdHis') . '_' . basename($_FILES['arquivo_parcial']['name']);
            $destino = 'uploads/' . $arquivo_nome;
            move_uploaded_file($_FILES['arquivo_parcial']['tmp_name'], $destino);
        }
    }
    if ($desc_parcial && empty($error)) {
        if ($id_parcial > 0) {
            if ($arquivo_nome) {
                // Buscar e deletar arquivo antigo
                $stmt = $db->prepare('SELECT arquivo FROM solucoes_parciais WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
                $stmt->execute([$id_parcial, $equipe_id, $desafio_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && !empty($row['arquivo'])) {
                    $filePath = __DIR__ . '/uploads/' . $row['arquivo'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $stmt = $db->prepare('UPDATE solucoes_parciais SET descricao = ?, arquivo = ?, url_video = ? WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
                $stmt->execute([$desc_parcial, $arquivo_nome, $url_video, $id_parcial, $equipe_id, $desafio_id]);
            } else {
                $stmt = $db->prepare('UPDATE solucoes_parciais SET descricao = ?, url_video = ? WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
                $stmt->execute([$desc_parcial, $url_video, $id_parcial, $equipe_id, $desafio_id]);
            }
        } else {
            $stmt = $db->prepare('INSERT INTO solucoes_parciais (equipe_id, desafio_id, descricao, arquivo, url_video, data_envio) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$equipe_id, $desafio_id, $desc_parcial, $arquivo_nome, $url_video]);
        }
        header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-parcial');
        exit();
    }
}
if (isset($_POST['acao']) && $_POST['acao'] === 'excluir_parcial') {
    $id_parcial = intval($_POST['id_parcial']);
    if ($id_parcial) {
        // Buscar nome do arquivo antes de excluir
        $stmt = $db->prepare('SELECT arquivo FROM solucoes_parciais WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
        $stmt->execute([$id_parcial, $equipe_id, $desafio_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['arquivo'])) {
            $filePath = __DIR__ . '/uploads/' . $row['arquivo'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Excluir do banco
        $stmt = $db->prepare('DELETE FROM solucoes_parciais WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
        $stmt->execute([$id_parcial, $equipe_id, $desafio_id]);
        header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-parcial');
        exit();
    }
}
// Buscar última solução parcial
$stmt = $db->prepare('SELECT id, descricao, arquivo, url_video, data_envio FROM solucoes_parciais WHERE equipe_id = ? AND desafio_id = ? ORDER BY data_envio DESC LIMIT 1');
$stmt->execute([$equipe_id, $desafio_id]);
$solucao_parcial = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar histórico de andamentos
$stmt = $db->prepare('SELECT status, descricao, data_atualizacao FROM andamento_solucoes WHERE equipe_id = ? AND desafio_id = ? ORDER BY data_atualizacao DESC');
$stmt->execute([$equipe_id, $desafio_id]);
$andamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar registros do diário de bordo
$stmt = $db->prepare('SELECT id, titulo, descricao, data_registro FROM diario_bordo WHERE equipe_id = ? AND desafio_id = ? ORDER BY data_registro DESC');
$stmt->execute([$equipe_id, $desafio_id]);
$diario_bordo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar tarefas do Kanban
$kanban_afazer = [];
$kanban_andamento = [];
$kanban_concluido = [];
$stmt = $db->prepare('SELECT id, titulo, status FROM kanban_tarefas WHERE equipe_id = ? AND desafio_id = ? ORDER BY data_criacao ASC');
$stmt->execute([$equipe_id, $desafio_id]);
while ($tarefa = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($tarefa['status'] === 'afazer') $kanban_afazer[] = $tarefa;
    elseif ($tarefa['status'] === 'andamento') $kanban_andamento[] = $tarefa;
    elseif ($tarefa['status'] === 'concluido') $kanban_concluido[] = $tarefa;
}

// --- INÍCIO: Endpoint AJAX para Kanban ---
if (isset($_GET['kanban_ajax']) && $_GET['kanban_ajax'] == '1') {
    ob_start();
    ?>
    <div id="kanban-afazer">
        <?php foreach ($kanban_afazer as $t): ?>
            <div class="kanban-tarefa mb-2" draggable="true" data-id="<?= $t['id'] ?>">
                <span class="drag-icon"><i class="fas fa-grip-vertical"></i></span>
                <span><?= htmlspecialchars($t['titulo']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="kanban-andamento">
        <?php foreach ($kanban_andamento as $t): ?>
            <div class="kanban-tarefa mb-2" draggable="true" data-id="<?= $t['id'] ?>">
                <span class="drag-icon"><i class="fas fa-grip-vertical"></i></span>
                <span><?= htmlspecialchars($t['titulo']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="kanban-concluido">
        <?php foreach ($kanban_concluido as $t): ?>
            <div class="kanban-tarefa mb-2" draggable="true" data-id="<?= $t['id'] ?>">
                <span class="drag-icon"><i class="fas fa-grip-vertical"></i></span>
                <span><?= htmlspecialchars($t['titulo']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    echo ob_get_clean();
    exit();
}
// --- FIM: Endpoint AJAX para Kanban ---

// --- Solução final: salvar, buscar, editar e excluir ---
if (isset($_POST['acao']) && $_POST['acao'] === 'final') {
    $desc_final = trim($_POST['descricao_final']);
    $id_final = isset($_POST['id_final']) ? intval($_POST['id_final']) : 0;
    $url_video_final = trim($_POST['url_video_final'] ?? '');
    $arquivo_final_nome = '';
    if (!empty($_FILES['arquivo_final']['name'])) {
        if ($_FILES['arquivo_final']['size'] > 16 * 1024 * 1024) {
            $error = 'O arquivo deve ter no máximo 16MB.';
        } else {
            $arquivo_final_nome = date('YmdHis') . '_' . basename($_FILES['arquivo_final']['name']);
            $destino = 'uploads/' . $arquivo_final_nome;
            move_uploaded_file($_FILES['arquivo_final']['tmp_name'], $destino);
        }
    }
    if ($desc_final && empty($error)) {
        if ($id_final > 0) {
            if ($arquivo_final_nome) {
                // Buscar e deletar arquivo antigo
                $stmt = $db->prepare('SELECT arquivo FROM solucoes_finais WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
                $stmt->execute([$id_final, $equipe_id, $desafio_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && !empty($row['arquivo'])) {
                    $filePath = __DIR__ . '/uploads/' . $row['arquivo'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $stmt = $db->prepare('UPDATE solucoes_finais SET descricao = ?, arquivo = ?, url_video = ? WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
                $stmt->execute([$desc_final, $arquivo_final_nome, $url_video_final, $id_final, $equipe_id, $desafio_id]);
            } else {
                $stmt = $db->prepare('UPDATE solucoes_finais SET descricao = ?, url_video = ? WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
                $stmt->execute([$desc_final, $url_video_final, $id_final, $equipe_id, $desafio_id]);
            }
        } else {
            $stmt = $db->prepare('INSERT INTO solucoes_finais (equipe_id, desafio_id, descricao, arquivo, url_video, data_envio) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$equipe_id, $desafio_id, $desc_final, $arquivo_final_nome, $url_video_final]);
        }
        // Registrar no diário de bordo
        $stmt = $db->prepare('INSERT INTO diario_bordo (equipe_id, desafio_id, titulo, descricao) VALUES (?, ?, ?, ?)');
        $stmt->execute([$equipe_id, $desafio_id, 'Solução final cadastrada', 'A solução final foi cadastrada para este desafio.']);
        header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-final');
        exit();
    }
}
if (isset($_POST['acao']) && $_POST['acao'] === 'excluir_final') {
    $id_final = intval($_POST['id_final']);
    if ($id_final) {
        // Buscar nome do arquivo antes de excluir
        $stmt = $db->prepare('SELECT arquivo FROM solucoes_finais WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
        $stmt->execute([$id_final, $equipe_id, $desafio_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['arquivo'])) {
            $filePath = __DIR__ . '/uploads/' . $row['arquivo'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Excluir do banco
        $stmt = $db->prepare('DELETE FROM solucoes_finais WHERE id = ? AND equipe_id = ? AND desafio_id = ?');
        $stmt->execute([$id_final, $equipe_id, $desafio_id]);
        header('Location: desafio-andamento.php?equipe_id=' . $equipe_id . '&desafio_id=' . $desafio_id . '#section-final');
        exit();
    }
}
// Buscar última solução final
$stmt = $db->prepare('SELECT id, descricao, arquivo, url_video, data_envio FROM solucoes_finais WHERE equipe_id = ? AND desafio_id = ? ORDER BY data_envio DESC LIMIT 1');
$stmt->execute([$equipe_id, $desafio_id]);
$solucao_final = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>
<style>
.kanban-board {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
}
.kanban-column {
    background: #f6f6f6;
    border-radius: 16px;
    min-width: 280px;
    flex: 1 1 0;
    display: flex;
    flex-direction: column;
    max-width: 100%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 1rem 0.5rem 0.5rem 0.5rem;
}
.kanban-title {
    text-align: center;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: #444;
    letter-spacing: 0.5px;
}
.kanban-tarefa {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    margin-bottom: 0.75rem;
    padding: 0.75rem 1rem;
    cursor: grab;
    display: flex;
    align-items: center;
    transition: box-shadow 0.2s, background 0.2s;
    border: 1px solid #ececec;
}
.kanban-tarefa:active {
    cursor: grabbing;
    background: #f0f0f0;
}
.kanban-tarefa .drag-icon {
    margin-right: 8px;
    color: #bbb;
    transition: color 0.2s;
}
.kanban-tarefa:hover .drag-icon {
    color: #007bff;
}
.kanban-col {
    min-height: 0;
    flex: 1 1 auto;
}
@media (max-width: 900px) {
    .kanban-board { flex-direction: column; gap: 1rem; }
    .kanban-column { min-width: 100%; }
}
.timeline-modern {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: flex-start;
    margin: 2rem 0 1.5rem 0;
    position: relative;
}
.timeline-modern::before {
    content: '';
    position: absolute;
    top: 32px;
    left: 40px;
    right: 40px;
    height: 4px;
    background: #e0e7ef;
    z-index: 0;
}
.timeline-step {
    position: relative;
    z-index: 1;
    flex: 1 1 0;
    text-align: center;
}
.timeline-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e0e7ef;
    margin: 0 auto 0.5rem auto;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    color: #888;
    border: 3px solid #e0e7ef;
    transition: background 0.2s, border 0.2s, color 0.2s;
}
.timeline-step.active .timeline-circle {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
    box-shadow: 0 0 0 4px #2563eb22;
}
.timeline-label {
    font-size: 1rem;
    font-weight: 500;
    color: #333;
    margin-bottom: 0.2rem;
}
.timeline-step.active .timeline-label {
    color: #2563eb;
}
@media (max-width: 700px) {
    .timeline-modern { flex-direction: column; align-items: stretch; }
    .timeline-modern::before { left: 24px; right: 24px; top: 32px; height: 4px; }
    .timeline-step { margin-bottom: 2.5rem; }
}
.filters-section {
    background: white;
    border-radius: 15px;
    padding: 12px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
<div class="container mt-5">
    <h2>Andamento da Solução</h2>
    <p><strong>Equipe:</strong> <?= htmlspecialchars($info['equipe_nome']) ?> <br>
       <strong>Desafio:</strong> <?= htmlspecialchars($info['desafio_titulo']) ?></p>

    <?php
    // Determinar etapa do cronograma
    $etapa_cronograma = 1;
    if ($solucao_final) {
        $etapa_cronograma = 3;
    } elseif ($solucao_parcial) {
        $etapa_cronograma = 2;
    }
    ?>
    <!-- Cronograma do Projeto (colapsável) -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-calendar-alt me-2"></i>Cronograma do Projeto</span>
        </div>
        <div class="card-body">
            <div class="timeline-modern">
                <div class="timeline-step<?= $etapa_cronograma == 1 ? ' active' : '' ?>">
                    <div class="timeline-circle">1</div>
                    <div class="timeline-label">Desenvolvimento da solução</div>
                </div>
                <div class="timeline-step<?= $etapa_cronograma == 2 ? ' active' : '' ?>">
                    <div class="timeline-circle">2</div>
                    <div class="timeline-label">Solução parcial / prototipagem</div>
                </div>
                <div class="timeline-step<?= $etapa_cronograma == 3 ? ' active' : '' ?>">
                    <div class="timeline-circle">3</div>
                    <div class="timeline-label">Entrega da solução</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões de navegação das seções -->
    <div class="d-flex flex-wrap gap-3 mb-4 justify-content-center">
        <button class="btn btn-lg btn-outline-primary d-flex align-items-center nav-section-btn" data-section="diario">
            <i class="fas fa-book-open fa-lg me-2"></i> Diário de bordo
        </button>
        <button class="btn btn-lg btn-outline-secondary d-flex align-items-center nav-section-btn" data-section="tarefas">
            <i class="fas fa-tasks fa-lg me-2"></i> Tarefas
        </button>
        <button class="btn btn-lg btn-outline-success d-flex align-items-center nav-section-btn" data-section="alunos">
            <i class="fas fa-users fa-lg me-2"></i> Gerenciar alunos
        </button>
        <button class="btn btn-lg btn-outline-warning d-flex align-items-center nav-section-btn" data-section="parcial">
            <i class="fas fa-file-alt fa-lg me-2"></i> Solução parcial
        </button>
        <button class="btn btn-lg btn-outline-info d-flex align-items-center nav-section-btn" data-section="final">
            <i class="fas fa-flag-checkered fa-lg me-2"></i> Solução final
        </button>
    </div>
    <div id="section-diario" class="section-content">
        <!-- Texto informativo -->
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            O Diário de bordo é o espaço para registrar acontecimentos, decisões, aprendizados e avanços do projeto. Use para documentar o progresso, reuniões, entregas parciais e qualquer informação relevante para o histórico da equipe.
        </div>
        <!-- Formulário de registro -->
        <div class="mb-3">
            <form method="POST" id="formDiario">
                <input type="hidden" name="acao" value="diario">
                <div class="mb-2">
                    <label class="form-label">Título</label>
                    <input type="text" class="form-control" name="titulo_diario" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Descrição</label>
                    <textarea class="form-control" name="descricao_diario" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </form>
        </div>
        <!-- Registros do diário de bordo -->
        <h5 class="mb-3 mt-4"><i class="fas fa-book-open me-2"></i>Registros do Diário de Bordo</h5>
        <div id="listaDiario">
            <?php if (count($diario_bordo) === 0): ?>
                <p class="text-muted">Nenhum registro no diário de bordo ainda.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($diario_bordo as $d): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body pb-2">
                                    <?php if (isset($_GET['edit_diario']) && $_GET['edit_diario'] == $d['id']): ?>
                                        <form method="POST">
                                            <input type="hidden" name="acao" value="editar_diario">
                                            <input type="hidden" name="id_diario" value="<?= $d['id'] ?>">
                                            <div class="mb-2">
                                                <label class="form-label">Título</label>
                                                <input type="text" name="titulo_diario_edit" class="form-control form-control-sm" value="<?= htmlspecialchars($d['titulo']) ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Descrição</label>
                                                <textarea name="descricao_diario_edit" class="form-control form-control-sm" rows="3" required><?= htmlspecialchars($d['descricao']) ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Salvar</button>
                                            <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>#section-diario" class="btn btn-sm btn-secondary ms-2">Cancelar</a>
                                        </form>
                                    <?php else: ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold"><i class="fas fa-calendar-alt me-1"></i><?= date('d/m/Y H:i', strtotime($d['data_registro'])) ?> - <?= htmlspecialchars($d['titulo']) ?></span>
                                        <div class="d-flex align-items-center">
                                            <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>&edit_diario=<?= $d['id'] ?>#section-diario" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="fas fa-edit"></i></a>
                                            <form method="POST" class="mb-0">
                                                <input type="hidden" name="acao" value="excluir_diario">
                                                <input type="hidden" name="id_diario" value="<?= $d['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="text-secondary" style="white-space:pre-line;"><?= nl2br(htmlspecialchars($d['descricao'])) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
        // Exemplo de ação para botão editar (frontend, sem backend ainda)
        document.querySelectorAll('.btn-editar-diario').forEach(function(btn) {
            btn.addEventListener('click', function() {
                alert('Funcionalidade de edição em breve!');
            });
        });
        </script>
    </div>
    <div id="section-tarefas" class="section-content" style="display:none;">
        <!-- Conteúdo da aba tarefas -->
        <div id="tarefas">
            <!-- Kanban de tarefas -->
            <div class="kanban-board mb-3">
                <div class="kanban-column">
                    <div class="kanban-title">A fazer</div>
                    <div class="mb-2">
                        <form method="POST" class="formTarefaKanban">
                            <input type="hidden" name="acao" value="tarefa">
                            <input type="hidden" name="status" value="afazer">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="titulo_tarefa" placeholder="Nova tarefa" required>
                                <button type="submit" class="btn btn-secondary">Adicionar</button>
                            </div>
                        </form>
                    </div>
                    <div class="kanban-col" id="kanban-afazer" data-status="afazer" ondragover="event.preventDefault();">
                        <?php foreach ($kanban_afazer as $t): ?>
                            <div class="kanban-tarefa mb-2" draggable="true" data-id="<?= $t['id'] ?>">
                                <span class="drag-icon"><i class="fas fa-grip-vertical"></i></span>
                                <span><?= htmlspecialchars($t['titulo']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="kanban-column">
                    <div class="kanban-title">Em andamento</div>
                    <div class="mb-2">
                        <form method="POST" class="formTarefaKanban">
                            <input type="hidden" name="acao" value="tarefa">
                            <input type="hidden" name="status" value="andamento">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="titulo_tarefa" placeholder="Nova tarefa" required>
                                <button type="submit" class="btn btn-secondary">Adicionar</button>
                            </div>
                        </form>
                    </div>
                    <div class="kanban-col" id="kanban-andamento" data-status="andamento" ondragover="event.preventDefault();">
                        <?php foreach ($kanban_andamento as $t): ?>
                            <div class="kanban-tarefa mb-2" draggable="true" data-id="<?= $t['id'] ?>">
                                <span class="drag-icon"><i class="fas fa-grip-vertical"></i></span>
                                <span><?= htmlspecialchars($t['titulo']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="kanban-column">
                    <div class="kanban-title">Concluído</div>
                    <div class="mb-2">
                        <form method="POST" class="formTarefaKanban">
                            <input type="hidden" name="acao" value="tarefa">
                            <input type="hidden" name="status" value="concluido">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="titulo_tarefa" placeholder="Nova tarefa" required>
                                <button type="submit" class="btn btn-secondary">Adicionar</button>
                            </div>
                        </form>
                    </div>
                    <div class="kanban-col" id="kanban-concluido" data-status="concluido" ondragover="event.preventDefault();">
                        <?php foreach ($kanban_concluido as $t): ?>
                            <div class="kanban-tarefa mb-2" draggable="true" data-id="<?= $t['id'] ?>">
                                <span class="drag-icon"><i class="fas fa-grip-vertical"></i></span>
                                <span><?= htmlspecialchars($t['titulo']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="section-alunos" class="section-content" style="display:none;">
        <!-- Conteúdo da aba alunos -->
        <div id="gerenciarAlunos">
            <?php if (count($alunos) === 0): ?>
                <p class="text-muted">Nenhum aluno cadastrado nesta equipe.</p>
            <?php else: ?>
                <form method="POST" id="formEditarAlunos">
                    <input type="hidden" name="acao" value="editar_alunos_lote">
                    <ul class="list-group mb-3">
                        <?php foreach ($alunos as $i => $al): ?>
                            <li class="list-group-item">
                                <div class="row g-2 align-items-center mb-0">
                                    <input type="hidden" name="alunos[<?= $i ?>][id]" value="<?= $al['id'] ?>">
                                    <div class="col-12 col-md-3">
                                        <input type="text" name="alunos[<?= $i ?>][nome]" class="form-control form-control-sm" value="<?= htmlspecialchars($al['nome']) ?>" required placeholder="Nome">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="email" name="alunos[<?= $i ?>][email]" class="form-control form-control-sm" value="<?= htmlspecialchars($al['email']) ?>" required placeholder="Email">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <input type="text" name="alunos[<?= $i ?>][whatsapp]" class="form-control form-control-sm" value="<?= htmlspecialchars($al['whatsapp']) ?>" placeholder="WhatsApp">
                                    </div>
                                    <div class="col-12 col-md-2 d-flex justify-content-end gap-2">
                                        <form method="POST" class="mb-0">
                                            <input type="hidden" name="acao" value="excluir_aluno">
                                            <input type="hidden" name="id_aluno" value="<?= $al['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <div class="mt-3">
            <h6 class="mb-2"><i class="fas fa-user-plus me-1"></i>Adicionar novo aluno à equipe</h6>
            <form method="POST" id="formAluno">
                <input type="hidden" name="acao" value="aluno">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <input type="text" class="form-control" name="nome_aluno" placeholder="Nome do aluno">
                    </div>
                    <div class="col-12 col-md-4">
                        <input type="email" class="form-control" name="email_aluno" placeholder="Email do aluno">
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="text" class="form-control" name="whatsapp_aluno" placeholder="WhatsApp">
                    </div>
                    <div class="col-12 col-md-1">
                        <button type="submit" class="btn btn-success w-100">Adicionar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="section-parcial" class="section-content" style="display:none;">
        <?php if ($solucao_parcial): ?>
            <?php if (isset($_GET['edit_parcial']) && $_GET['edit_parcial'] == $solucao_parcial['id']): ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="parcial">
                    <input type="hidden" name="id_parcial" value="<?= $solucao_parcial['id'] ?>">
                    <div class="mb-2">
                        <label class="form-label">Descrição da solução parcial</label>
                        <textarea class="form-control" name="descricao_parcial" rows="4" required><?= htmlspecialchars($solucao_parcial['descricao']) ?></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Anexar arquivo (máx. 16MB)</label>
                        <input type="file" class="form-control" name="arquivo_parcial" maxlength="16777216">
                        <?php if (!empty($solucao_parcial['arquivo'])): ?>
                            <div class="mt-1"><a href="/uploads/<?= htmlspecialchars($solucao_parcial['arquivo']) ?>" target="_blank"><i class="fas fa-paperclip me-1"></i>Arquivo atual: <?= htmlspecialchars($solucao_parcial['arquivo']) ?></a></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">URL do vídeo (opcional)</label>
                        <input type="url" class="form-control" name="url_video" value="<?= htmlspecialchars($solucao_parcial['url_video'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-success">Salvar edição</button>
                    <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>#section-parcial" class="btn btn-secondary ms-2">Cancelar</a>
                </form>
            <?php else: ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold"><i class="fas fa-calendar-alt me-1"></i>Enviada em <?= date('d/m/Y H:i', strtotime($solucao_parcial['data_envio'])) ?></span>
                            <div class="d-flex gap-2">
                                <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>&edit_parcial=<?= $solucao_parcial['id'] ?>#section-parcial" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Editar</a>
                                <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>&enviar_mentor=1#section-parcial" class="btn btn-sm btn-outline-success"><i class="fas fa-paper-plane"></i> Enviar ao mentor</a>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="acao" value="excluir_parcial">
                                    <input type="hidden" name="id_parcial" value="<?= $solucao_parcial['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Excluir</button>
                                </form>
                            </div>
                        </div>
                        <div style="white-space:pre-line;"> <?= nl2br(htmlspecialchars($solucao_parcial['descricao'])) ?> </div>
                        <?php if (!empty($solucao_parcial['arquivo'])): ?>
                            <div class="mt-2"><a href="/uploads/<?= htmlspecialchars($solucao_parcial['arquivo']) ?>" target="_blank"><i class="fas fa-paperclip me-1"></i>Arquivo enviado</a></div>
                        <?php endif; ?>
                        <?php if (!empty($solucao_parcial['url_video'])): ?>
                            <div class="mt-2"><a href="<?= htmlspecialchars($solucao_parcial['url_video']) ?>" target="_blank"><i class="fab fa-youtube me-1"></i>Vídeo enviado</a></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <form method="POST" enctype="multipart/form-data" id="formParcial">
                <input type="hidden" name="acao" value="parcial">
                <div class="mb-2">
                    <label class="form-label">Descrição da solução parcial</label>
                    <textarea class="form-control" name="descricao_parcial" rows="4" required></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Anexar arquivo (máx. 16MB)</label>
                    <input type="file" class="form-control" name="arquivo_parcial" maxlength="16777216">
                </div>
                <div class="mb-2">
                    <label class="form-label">URL do vídeo (opcional)</label>
                    <input type="url" class="form-control" name="url_video">
                </div>
                <button type="submit" class="btn btn-primary">Cadastrar solução parcial</button>
            </form>
        <?php endif; ?>
    </div>
    <div id="section-final" class="section-content" style="display:none;">
        <?php if ($solucao_final): ?>
            <?php if (isset($_GET['edit_final']) && $_GET['edit_final'] == $solucao_final['id']): ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="final">
                    <input type="hidden" name="id_final" value="<?= $solucao_final['id'] ?>">
                    <div class="mb-2">
                        <label class="form-label">Descrição da solução final</label>
                        <textarea class="form-control" name="descricao_final" rows="4" required><?= htmlspecialchars($solucao_final['descricao']) ?></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Anexar arquivo (máx. 16MB)</label>
                        <input type="file" class="form-control" name="arquivo_final" maxlength="16777216">
                        <?php if (!empty($solucao_final['arquivo'])): ?>
                            <div class="mt-1"><a href="/uploads/<?= htmlspecialchars($solucao_final['arquivo']) ?>" target="_blank"><i class="fas fa-paperclip me-1"></i>Arquivo atual: <?= htmlspecialchars($solucao_final['arquivo']) ?></a></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">URL do vídeo (opcional)</label>
                        <input type="url" class="form-control" name="url_video_final" value="<?= htmlspecialchars($solucao_final['url_video'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-success">Salvar edição</button>
                    <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>#section-final" class="btn btn-secondary ms-2">Cancelar</a>
                </form>
            <?php else: ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold"><i class="fas fa-calendar-alt me-1"></i>Enviada em <?= date('d/m/Y H:i', strtotime($solucao_final['data_envio'])) ?></span>
                            <div class="d-flex gap-2">
                                <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>&edit_final=<?= $solucao_final['id'] ?>#section-final" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Editar</a>
                                <a href="?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>&enviar_mentor_final=1#section-final" class="btn btn-sm btn-outline-success"><i class="fas fa-paper-plane"></i> Enviar ao mentor</a>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="acao" value="excluir_final">
                                    <input type="hidden" name="id_final" value="<?= $solucao_final['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Excluir</button>
                                </form>
                            </div>
                        </div>
                        <div style="white-space:pre-line;"> <?= nl2br(htmlspecialchars($solucao_final['descricao'])) ?> </div>
                        <?php if (!empty($solucao_final['arquivo'])): ?>
                            <div class="mt-2"><a href="/uploads/<?= htmlspecialchars($solucao_final['arquivo']) ?>" target="_blank"><i class="fas fa-paperclip me-1"></i>Arquivo enviado</a></div>
                        <?php endif; ?>
                        <?php if (!empty($solucao_final['url_video'])): ?>
                            <div class="mt-2"><a href="<?= htmlspecialchars($solucao_final['url_video']) ?>" target="_blank"><i class="fab fa-youtube me-1"></i>Vídeo enviado</a></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <form method="POST" enctype="multipart/form-data" id="formFinal">
                <input type="hidden" name="acao" value="final">
                <div class="mb-2">
                    <label class="form-label">Descrição da solução final</label>
                    <textarea class="form-control" name="descricao_final" rows="4" required></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Anexar arquivo (máx. 16MB)</label>
                    <input type="file" class="form-control" name="arquivo_final" maxlength="16777216">
                </div>
                <div class="mb-2">
                    <label class="form-label">URL do vídeo (opcional)</label>
                    <input type="url" class="form-control" name="url_video_final">
                </div>
                <button type="submit" class="btn btn-primary">Cadastrar solução final</button>
            </form>
        <?php endif; ?>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
</div>
<script>
// Alternar exibição das seções ao clicar nos botões
const btns = document.querySelectorAll('.nav-section-btn');
const sections = document.querySelectorAll('.section-content');
btns.forEach(btn => {
    btn.addEventListener('click', function() {
        const sec = this.getAttribute('data-section');
        sections.forEach(s => s.style.display = 'none');
        document.getElementById('section-' + sec).style.display = '';
        btns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
// Exibir a primeira seção por padrão ou a área correta se houver hash
if (window.location.hash && window.location.hash.startsWith('#section-')) {
    const hashSection = window.location.hash.replace('#section-', '');
    sections.forEach(s => s.style.display = 'none');
    const secEl = document.getElementById('section-' + hashSection);
    if (secEl) secEl.style.display = '';
    btns.forEach(b => b.classList.remove('active'));
    const btnEl = document.querySelector('.nav-section-btn[data-section="' + hashSection + '"]');
    if (btnEl) btnEl.classList.add('active');
} else {
    if (sections.length) sections[0].style.display = '';
    if (btns.length) btns[0].classList.add('active');
}

// Função para recarregar a página após ações de Kanban
function inicializarDragDropKanban() {
    let tarefaArrastada = null;
    document.querySelectorAll('.kanban-tarefa').forEach(function(card) {
        card.addEventListener('dragstart', function(e) {
            tarefaArrastada = card;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.getAttribute('data-id'));
            setTimeout(() => card.classList.add('opacity-50'), 0);
        });
        card.addEventListener('dragend', function(e) {
            tarefaArrastada = null;
            card.classList.remove('opacity-50');
        });
        card.addEventListener('mouseenter', function() {
            const icon = card.querySelector('.drag-icon i');
            if (icon) icon.className = 'fas fa-arrows-alt';
        });
        card.addEventListener('mouseleave', function() {
            const icon = card.querySelector('.drag-icon i');
            if (icon) icon.className = 'fas fa-grip-vertical';
        });
    });
    document.querySelectorAll('.kanban-col').forEach(function(col) {
        col.addEventListener('dragover', function(e) {
            e.preventDefault();
            col.classList.add('bg-light');
        });
        col.addEventListener('dragleave', function(e) {
            col.classList.remove('bg-light');
        });
        col.addEventListener('drop', function(e) {
            e.preventDefault();
            col.classList.remove('bg-light');
            const tarefaId = e.dataTransfer.getData('text/plain');
            if (!tarefaId) return;
            const novoStatus = col.getAttribute('data-status');
            // Atualizar status via AJAX e recarregar as colunas via AJAX
            fetch(window.location.pathname + '?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'acao=mover_tarefa&tarefa_id=' + encodeURIComponent(tarefaId) + '&novo_status=' + encodeURIComponent(novoStatus)
            })
            .then(resp => {
                if (!resp.ok) throw new Error('POST falhou');
                return fetch(window.location.pathname + '?equipe_id=<?= $equipe_id ?>&desafio_id=<?= $desafio_id ?>&kanban_ajax=1');
            })
            .then(resp => resp.text())
            .then(html => {
                // Atualiza as três colunas do Kanban
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                ['afazer','andamento','concluido'].forEach(function(status) {
                    const novaCol = tempDiv.querySelector('#kanban-' + status);
                    if (novaCol) {
                        document.getElementById('kanban-' + status).innerHTML = novaCol.innerHTML;
                    }
                });
                inicializarDragDropKanban();
            })
            .catch((e) => {
                alert('Erro ao mover tarefa! ' + e.message);
            });
        });
    });
}
inicializarDragDropKanban();

// Máscara de WhatsApp para campo de adicionar novo aluno
function maskWhatsapp(input) {
    input.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        if (v.length > 0) v = '(' + v;
        if (v.length > 3) v = v.slice(0, 3) + ') ' + v.slice(3);
        if (v.length > 10) v = v.slice(0, 10) + '-' + v.slice(10);
        this.value = v;
    });
}
const whatsappNovoAluno = document.querySelector('input[name="whatsapp_aluno"]');
if (whatsappNovoAluno) maskWhatsapp(whatsappNovoAluno);
// Máscara para todos os campos de whatsapp dos alunos já existentes (edição em lote)
const whatsappEditAlunos = document.querySelectorAll('#formEditarAlunos input[name^="alunos"][name$="[whatsapp]"]');
whatsappEditAlunos.forEach(function(input) { maskWhatsapp(input); });
</script>
<?php include 'includes/footer.php'; ?> 