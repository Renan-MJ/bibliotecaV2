<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_livros.php');
    exit;
}

// Capturando os novos campos da interface moderna
$id     = $_POST['id'] ?? null;
$titulo = $_POST['titulo'] ?? '';
$autor  = $_POST['autor'] ?? '';
$cdd    = $_POST['cdd'] ?? ''; // Novo campo
$status = $_POST['status'] ?? 'Disponível'; // Novo campo

// Validação (Removido o ano_publicacao daqui)
if (!$id || empty($titulo) || empty($autor)) {
    // Redireciona com erro caso falte algo essencial
    header('Location: listar_livros.php?erro=dados_invalidos');
    exit;
}

try {
    // SQL atualizada: removido ano_publicacao, adicionado cdd e status
    $sql = "UPDATE livros 
            SET titulo = :titulo, 
                autor = :autor, 
                cdd = :cdd, 
                status = :status
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo' => $titulo,
        ':autor'  => $autor,
        ':cdd'    => $cdd,
        ':status' => $status,
        ':id'     => $id
    ]);

    // Redireciona com sucesso
    header('Location: listar_livros.php?sucesso=atualizado');
    exit;

} catch (PDOException $e) {
    // Em caso de erro no banco (ex: coluna não existe)
    die("Erro ao atualizar no banco de dados: " . $e->getMessage());
}