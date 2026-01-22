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
    // Antes de excluir, poderíamos verificar se o leitor possui empréstimos ativos
    // mas por enquanto, manteremos a exclusão direta conforme seu código original
    $sql = "DELETE FROM leitores WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    // Mensagem de sucesso para o usuário
    $_SESSION['sucesso'] = "O registro do leitor foi <strong>removido permanentemente</strong> do sistema.";
    
} catch (PDOException $e) {
    // Se houver erro (ex: chave estrangeira se o leitor tiver empréstimos)
    $_SESSION['erro'] = "Este leitor não pode ser excluído porque possui históricos vinculados a ele.";
}

header('Location: listar_leitores.php');
exit;