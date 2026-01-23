<?php
require_once __DIR__ . '/../config/database.php';

// Inicia a sessão para exibir a mensagem de confirmação
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_livros.php');
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    header('Location: listar_livros.php?erro=id_invalido');
    exit;
}

try {
    // 1. Verificação proativa antes de tentar excluir
    // Busca se existe algum empréstimo que ainda NÃO foi devolvido
    $stmtCheck = $pdo->prepare("SELECT id FROM emprestimos WHERE livro_id = :id AND data_devolucao_real IS NULL LIMIT 1");
    $stmtCheck->execute([':id' => $id]);
    $emprestimo = $stmtCheck->fetch();

    if ($emprestimo) {
        // Se encontrou empréstimo ativo, bloqueia e envia o link para a gestora
        $_SESSION['erro'] = "<strong>Bloqueio de Segurança:</strong> Este livro possui emprestimos registrados no momento.<br>
                        <a href='listar_emprestimos.php?id_selecionado=" . $emprestimo['id'] . "' class='btn btn-sm btn-outline-danger mt-2'>
                                <i class='fa-solid fa-arrow-right me-1'></i> Ver emprestimo
                        </a>";
        header('Location: listar_livros.php');
        exit;
    }

    // 2. Se não houver empréstimo pendente, tenta a exclusão definitiva
    $sql = "DELETE FROM livros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $_SESSION['sucesso'] = "O exemplar foi <strong>removido com sucesso</strong> do acervo municipal.";

} catch (PDOException $e) {
    // 3. Caso o livro possua histórico de empréstimos já DEVOLVIDOS que impedem a exclusão (FK)
    $_SESSION['erro'] = "Este livro não pode ser removido definitivamente pois possui <strong>Empréstimos</strong> no sistema.";
}

header('Location: listar_livros.php');
exit;