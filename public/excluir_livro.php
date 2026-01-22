<?php

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso inválido');
}

$id = $_POST['id'] ?? null;

if (!$id) {
    die('ID inválido');
}

$sql = "DELETE FROM livros WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id
]);

header('Location: listar_livros.php');
exit;
