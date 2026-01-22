<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_emprestimos.php');
    exit;
}

$livro_id = $_POST['livro_id'] ?? '';
$leitor_id = $_POST['leitor_id'] ?? '';
$data_emprestimo = $_POST['data_emprestimo'] ?? '';
$data_devolucao_prevista = $_POST['data_devolucao_prevista'] ?? '';

if (!$livro_id || !$leitor_id || !$data_emprestimo || !$data_devolucao_prevista) {
    $_SESSION['erro'] = "Preencha todos os campos do protocolo de empréstimo.";
    header('Location: cadastrar_emprestimo.php');
    exit;
}

try {
    // Iniciamos uma transação para garantir que as duas tabelas sejam atualizadas juntas
    $pdo->beginTransaction();

    // 1. Inserir o registro na tabela de empréstimos
    $sqlEmprestimo = "INSERT INTO emprestimos (livro_id, leitor_id, data_emprestimo, data_devolucao_prevista)
                      VALUES (:livro_id, :leitor_id, :data_emprestimo, :data_devolucao_prevista)";
    $stmt = $pdo->prepare($sqlEmprestimo);
    $stmt->execute([
        ':livro_id' => $livro_id,
        ':leitor_id' => $leitor_id,
        ':data_emprestimo' => $data_emprestimo,
        ':data_devolucao_prevista' => $data_devolucao_prevista
    ]);

    // 2. Atualizar o status do livro para 'Emprestado'
    $sqlLivro = "UPDATE livros SET status = 'Emprestado' WHERE id = :livro_id";
    $stmtLivro = $pdo->prepare($sqlLivro);
    $stmtLivro->execute([':livro_id' => $livro_id]);

    // Se tudo deu certo, confirma as alterações no banco
    $pdo->commit();

    $_SESSION['sucesso'] = "Empréstimo registrado! O livro agora consta como <strong>Emprestado</strong>.";
    header('Location: listar_emprestimos.php');
    exit;

} catch (Exception $e) {
    // Se algo falhar, desfaz as alterações
    $pdo->rollBack();
    $_SESSION['erro'] = "Erro ao processar empréstimo: " . $e->getMessage();
    header('Location: cadastrar_emprestimo.php');
    exit;
}