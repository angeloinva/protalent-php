<?php
require_once 'config/database.php';

$email = 'O_EMAIL_DO_USUARIO'; // coloque o email do usuário cadastrado
$senha = 'A_SENHA_DIGITADA';  // coloque a senha digitada

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo "Usuário encontrado!<br>";
    echo "Hash no banco: " . $row['password'] . "<br>";
    if (password_verify($senha, $row['password'])) {
        echo "<b>Senha OK!</b>";
    } else {
        echo "<b>Senha incorreta!</b>";
    }
} else {
    echo "Usuário não encontrado!";
}
?>