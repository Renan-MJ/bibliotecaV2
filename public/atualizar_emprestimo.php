<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$id = $_POST['id'] ?? null;
$livro_id = $_POST['livro_id'] ?? '';
$leitor_id = $_POST['leitor_id'] ?? '';
$data_emprestimo = $_POST['data_emprestimo'] ?? '';
$data_devolucao_prevista = $_POST['data_devolucao_prevista'] ?? '';
$data_devolucao_real = $_POST['data_devolucao_real'] ?: null;

if (!$id || !$livro_id || !$leitor_id || !$data_emprestimo || !$data_devolucao_prevista) {
    die('Todos os campos obrigatórios, exceto devolução real.');
}

$sql = "UPDATE emprestimos 
        SET livro_id = :livro_id, leitor_id = :leitor_id, 
            data_emprestimo = :data_emprestimo, 
            data_devolucao_prevista = :data_devolucao_prevista,
            data_devolucao_real = :data_devolucao_real
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':livro_id' => $livro_id,
    ':leitor_id' => $leitor_id,
    ':data_emprestimo' => $data_emprestimo,
    ':data_devolucao_prevista' => $data_devolucao_prevista,
    ':data_devolucao_real' => $data_devolucao_real,
    ':id' => $id
]);

$_SESSION['sucesso'] = 'Empréstimo atualizado com sucesso!';
header('Location: listar_emprestimos.php');
exit;
