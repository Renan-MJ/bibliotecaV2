<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_emprestimos.php');
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    $_SESSION['erro'] = "ID de empréstimo inválido.";
    header('Location: listar_emprestimos.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Primeiro, precisamos descobrir qual é o ID do livro deste empréstimo
    $sqlBusca = "SELECT livro_id FROM emprestimos WHERE id = :id";
    $stmtBusca = $pdo->prepare($sqlBusca);
    $stmtBusca->execute([':id' => $id]);
    $emprestimo = $stmtBusca->fetch(PDO::FETCH_ASSOC);

    if ($emprestimo) {
        $livro_id = $emprestimo['livro_id'];

        // 2. Registra a data de devolução real no empréstimo
        $sqlDevolucao = "UPDATE emprestimos SET data_devolucao_real = CURDATE() WHERE id = :id";
        $stmtDev = $pdo->prepare($sqlDevolucao);
        $stmtDev->execute([':id' => $id]);

        // 3. Volta o status do livro para 'Disponível'
        $sqlLivro = "UPDATE livros SET status = 'Disponível' WHERE id = :livro_id";
        $stmtLivro = $pdo->prepare($sqlLivro);
        $stmtLivro->execute([':livro_id' => $livro_id]);

        $pdo->commit();
        $_SESSION['sucesso'] = "Devolução realizada! O exemplar já está <strong>disponível</strong> para novos empréstimos.";
    } else {
        $pdo->rollBack();
        $_SESSION['erro'] = "Empréstimo não encontrado.";
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erro'] = "Erro ao processar devolução: " . $e->getMessage();
}

header('Location: listar_emprestimos.php');
exit;