<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso inválido');
}

$numero_cadastro = $_POST['numero_cadastro'] ?? '';
$nome = $_POST['nome'] ?? '';
$filiacao = $_POST['filiacao'] ?? '';
$rg = $_POST['rg'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$endereco = $_POST['endereco'] ?? '';

if ($numero_cadastro === '' || $nome === '') {
    die('Número de cadastro e nome são obrigatórios.');
}

$sql = "INSERT INTO leitores (numero_cadastro, nome, filiacao, rg, telefone, email, endereco, data_cadastro)
        VALUES (:numero_cadastro, :nome, :filiacao, :rg, :telefone, :email, :endereco, CURDATE())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':numero_cadastro' => $numero_cadastro,
    ':nome' => $nome,
    ':filiacao' => $filiacao,
    ':rg' => $rg,
    ':telefone' => $telefone,
    ':email' => $email,
    ':endereco' => $endereco
]);

session_start();
$_SESSION['sucesso'] = 'Leitor cadastrado com sucesso!';
header('Location: listar_leitores.php');
exit;
