<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_emprestimos.php');
    exit;
}

// Captura de dados
$id                      = $_POST['id'] ?? null;
$livro_id                = $_POST['livro_id'] ?? '';
$leitor_id               = $_POST['leitor_id'] ?? '';
$data_emprestimo         = $_POST['data_emprestimo'] ?? '';
$data_devolucao_prevista = $_POST['data_devolucao_prevista'] ?? '';
$data_devolucao_real     = !empty($_POST['data_devolucao_real']) ? $_POST['data_devolucao_real'] : null;

if (!$id || !$livro_id || !$leitor_id || !$data_emprestimo || !$data_devolucao_prevista) {
    $_SESSION['erro'] = "Preencha todos os campos obrigatórios.";
    header("Location: editar_emprestimo.php?id=$id");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Antes de atualizar, pegamos o ID do livro antigo para caso ele tenha sido trocado
    $stmtAntigo = $pdo->prepare("SELECT livro_id FROM emprestimos WHERE id = :id");
    $stmtAntigo->execute([':id' => $id]);
    $livro_antigo_id = $stmtAntigo->fetchColumn();

    // 2. Atualiza o registro do empréstimo
    $sql = "UPDATE emprestimos 
            SET livro_id = :livro_id, leitor_id = :leitor_id, 
                data_emprestimo = :data_emprestimo, 
                data_devolucao_prevista = :data_devolucao_prevista,
                data_devolucao_real = :data_devolucao_real
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':livro_id'                => $livro_id,
        ':leitor_id'               => $leitor_id,
        ':data_emprestimo'         => $data_emprestimo,
        ':data_devolucao_prevista' => $data_devolucao_prevista,
        ':data_devolucao_real'     => $data_devolucao_real,
        ':id'                      => $id
    ]);

    // 3. Lógica de Status: Se houver Data de Devolução Real, o livro atual fica 'Disponível'
    // Caso contrário, ele continua 'Emprestado'
    $novoStatus = ($data_devolucao_real) ? 'Disponível' : 'Emprestado';
    
    $updLivro = $pdo->prepare("UPDATE livros SET status = :status WHERE id = :livro_id");
    $updLivro->execute([':status' => $novoStatus, ':livro_id' => $livro_id]);

    // 4. Se o bibliotecário trocou o livro no formulário, o livro antigo deve voltar a ficar 'Disponível'
    if ($livro_antigo_id && $livro_antigo_id != $livro_id) {
        $liberarAntigo = $pdo->prepare("UPDATE livros SET status = 'Disponível' WHERE id = :antigo");
        $liberarAntigo->execute([':antigo' => $livro_antigo_id]);
    }

    $pdo->commit();
    $_SESSION['sucesso'] = "O registro de empréstimo foi atualizado com sucesso!";
    header('Location: listar_emprestimos.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erro'] = "Erro técnico ao atualizar: " . $e->getMessage();
    header("Location: listar_emprestimos.php");
    exit;
}