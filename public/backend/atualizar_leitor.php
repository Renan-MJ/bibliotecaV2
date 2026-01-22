<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso inválido');
}

$id = $_POST['id'] ?? null;
$numero_cadastro = $_POST['numero_cadastro'] ?? '';
$nome = $_POST['nome'] ?? '';
$filiacao = $_POST['filiacao'] ?? '';
$rg = $_POST['rg'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$endereco = $_POST['endereco'] ?? '';

if (!$id || $numero_cadastro === '' || $nome === '') {
    die('Dados inválidos');
}

$sql = "UPDATE leitores 
        SET numero_cadastro = :numero_cadastro, nome = :nome, filiacao = :filiacao,
            rg = :rg, telefone = :telefone, email = :email, endereco = :endereco
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':numero_cadastro' => $numero_cadastro,
    ':nome' => $nome,
    ':filiacao' => $filiacao,
    ':rg' => $rg,
    ':telefone' => $telefone,
    ':email' => $email,
    ':endereco' => $endereco,
    ':id' => $id
]);

header('Location: listar_leitores.php');
exit;
