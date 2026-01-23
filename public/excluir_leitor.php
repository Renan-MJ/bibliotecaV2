<?php
require_once __DIR__ . '/../config/database.php';

// Inicia a sessão para enviar a confirmação de exclusão
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_leitores.php');
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    $_SESSION['erro'] = "Não foi possível localizar o ID para exclusão.";
    header('Location: listar_leitores.php');
    exit;
}

try {
    // 1. Verificação Proativa: O leitor possui empréstimos pendentes?
    // Busca se existe algum empréstimo que ainda NÃO foi devolvido
    $stmtCheck = $pdo->prepare("SELECT id FROM emprestimos WHERE leitor_id = :id AND data_devolucao_real IS NULL LIMIT 1");
    $stmtCheck->execute([':id' => $id]);
    $emprestimo = $stmtCheck->fetch();

    if ($emprestimo) {
        // Se encontrou pendência, bloqueia e oferece link para o registro
        $_SESSION['erro'] = "<strong>Bloqueio de Segurança:</strong> Este leitor possui livros emprestados no momento.<br>
                    <a href='listar_emprestimos.php?id_selecionado=" . $emprestimo['id'] . "' class='btn btn-sm btn-outline-danger mt-2'>
                            <i class='fa-solid fa-arrow-right me-1'></i> Ver emprestimo
                    </a>";
        header('Location: listar_leitores.php');
        exit;
    }

    // 2. Tenta a exclusão direta (caso não haja pendências)
    $sql = "DELETE FROM leitores WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    // Mensagem de sucesso para o usuário
    $_SESSION['sucesso'] = "O registro do leitor foi <strong>removido permanentemente</strong> do sistema.";
    
} catch (PDOException $e) {
    // 3. Caso o leitor possua histórico de empréstimos já DEVOLVIDOS que impedem a exclusão via Banco (FK)
        $_SESSION['erro'] = "<strong>Bloqueio de Segurança:</strong> Este leitor possui livros emprestados no momento.<br>
                    <a href='listar_emprestimos.php?id_selecionado=" . $emprestimo['id'] . "' class='btn btn-sm btn-outline-danger mt-2'>
                            <i class='fa-solid fa-arrow-right me-1'></i> Ver emprestimo
                    </a>";
        header('Location: listar_leitores.php');
}

header('Location: listar_leitores.php');
exit;