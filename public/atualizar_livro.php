<?php

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso inválido');
}

$id = $_POST['id'] ?? null;
$titulo = $_POST['titulo'] ?? '';
$autor = $_POST['autor'] ?? '';
$ano_publicacao = $_POST['ano_publicacao'] ?? '';

if (!$id || $titulo === '' || $autor === '' || $ano_publicacao === '') {
    die('Dados inválidos');
}

$sql = "UPDATE livros 
        SET titulo = :titulo, autor = :autor, ano_publicacao = :ano_publicacao
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':titulo' => $titulo,
    ':autor' => $autor,
    ':ano_publicacao' => $ano_publicacao,
    ':id' => $id
]);

header('Location: listar_livros.php');
exit;
