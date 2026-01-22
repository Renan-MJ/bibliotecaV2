<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$livro_id = $_POST['livro_id'] ?? '';
$leitor_id = $_POST['leitor_id'] ?? '';
$data_emprestimo = $_POST['data_emprestimo'] ?? '';
$data_devolucao_prevista = $_POST['data_devolucao_prevista'] ?? '';

if (!$livro_id || !$leitor_id || !$data_emprestimo || !$data_devolucao_prevista) {
    die('Todos os campos são obrigatórios.');
}

$sql = "INSERT INTO emprestimos (livro_id, leitor_id, data_emprestimo, data_devolucao_prevista)
        VALUES (:livro_id, :leitor_id, :data_emprestimo, :data_devolucao_prevista)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':livro_id' => $livro_id,
    ':leitor_id' => $leitor_id,
    ':data_emprestimo' => $data_emprestimo,
    ':data_devolucao_prevista' => $data_devolucao_prevista
]);

$_SESSION['sucesso'] = 'Empréstimo registrado com sucesso!';
header('Location: listar_emprestimos.php');
exit;
