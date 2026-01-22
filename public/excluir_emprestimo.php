<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_emprestimos.php');
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    $_SESSION['erro'] = "ID de empréstimo inválido para exclusão.";
    header('Location: listar_emprestimos.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Antes de deletar, precisamos saber qual livro estava associado
    // e se ele já tinha sido devolvido ou não.
    $stmtBusca = $pdo->prepare("SELECT livro_id, data_devolucao_real FROM emprestimos WHERE id = :id");
    $stmtBusca->execute([':id' => $id]);
    $emprestimo = $stmtBusca->fetch(PDO::FETCH_ASSOC);

    if ($emprestimo) {
        $livro_id = $emprestimo['livro_id'];
        $foi_devolvido = !empty($emprestimo['data_devolucao_real']);

        // 2. Se o empréstimo for excluído SEM ter sido devolvido, 
        // precisamos liberar o livro manualmente para o acervo.
        if (!$foi_devolvido) {
            $stmtUpdate = $pdo->prepare("UPDATE livros SET status = 'Disponível' WHERE id = :livro_id");
            $stmtUpdate->execute([':livro_id' => $livro_id]);
        }

        // 3. Agora sim, deletamos o registro do empréstimo
        $sqlDelete = "DELETE FROM emprestimos WHERE id = :id";
        $stmtDel = $pdo->prepare($sqlDelete);
        $stmtDel->execute([':id' => $id]);

        $pdo->commit();
        $_SESSION['sucesso'] = "O registro de empréstimo foi removido e o status do livro foi sincronizado.";
    } else {
        $pdo->rollBack();
        $_SESSION['erro'] = "Registro não encontrado.";
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erro'] = "Erro ao excluir: " . $e->getMessage();
}

header('Location: listar_emprestimos.php');
exit;