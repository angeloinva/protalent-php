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
if ($id) {
    $stmt = $db->prepare('DELETE FROM empresas WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: empresas-lista.php');
exit(); 