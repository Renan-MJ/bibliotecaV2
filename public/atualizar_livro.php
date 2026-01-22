<?php
require_once __DIR__ . '/../config/database.php';

// ADICIONE ISSO: Inicia a sessão para que a mensagem funcione
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_livros.php');
    exit;
}

$id     = $_POST['id'] ?? null;
$titulo = $_POST['titulo'] ?? '';
$autor  = $_POST['autor'] ?? '';
$cdd    = $_POST['cdd'] ?? '';
$status = $_POST['status'] ?? 'Disponível';

if (!$id || empty($titulo) || empty($autor)) {
    header('Location: listar_livros.php?erro=dados_invalidos');
    exit;
}

try {
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

    // ALTERADO: Agora salva na sessão para a listagem exibir o alerta verde
    $_SESSION['sucesso'] = "O cadastro de <strong>" . htmlspecialchars($titulo) . "</strong> foi atualizado com sucesso!";

    header('Location: listar_livros.php');
    exit;

} catch (PDOException $e) {
    die("Erro ao atualizar no banco de dados: " . $e->getMessage());
}