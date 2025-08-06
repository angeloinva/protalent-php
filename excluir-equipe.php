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
$desafio_id = isset($_GET['desafio_id']) ? intval($_GET['desafio_id']) : 0;
if (!$equipe_id || !$desafio_id) {
    header('Location: index.php');
    exit();
}
// Excluir equipe e alunos vinculados (ON DELETE CASCADE já faz isso no banco)
$stmt = $db->prepare('DELETE FROM equipes WHERE id = ?');
$stmt->execute([$equipe_id]);
header('Location: visualizar-desafio.php?id=' . $desafio_id);
exit();