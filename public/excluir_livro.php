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
    // 1. Opcional: Aqui você poderia verificar se o livro está "Emprestado" 
    // antes de permitir a exclusão definitiva.

    $sql = "DELETE FROM livros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    // 2. Define a mensagem de sucesso na sessão
    $_SESSION['sucesso'] = "O exemplar foi <strong>removido com sucesso</strong> do acervo municipal.";

} catch (PDOException $e) {
    // 3. Caso o livro esteja vinculado a um empréstimo (Chave Estrangeira)
    $_SESSION['erro'] = "Não é possível excluir este livro pois ele possui histórico de empréstimos vinculado.";
}

header('Location: listar_livros.php');
exit;