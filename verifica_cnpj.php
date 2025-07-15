<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['cnpj'])) {
    echo json_encode(['error' => 'CNPJ não informado']);
    exit;
}

$cnpj = preg_replace('/\D/', '', $_GET['cnpj']);
if (strlen($cnpj) != 14) {
    echo json_encode(['error' => 'CNPJ inválido']);
    exit;
}

$url = "https://www.receitaws.com.br/v1/cnpj/$cnpj";
$response = @file_get_contents($url);

if ($response === FALSE) {
    echo json_encode(['error' => 'Erro ao consultar a ReceitaWS']);
    exit;
}

echo $response;