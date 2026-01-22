<?php

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso inválido');
}

$titulo = $_POST['titulo'] ?? '';
$autor = $_POST['autor'] ?? '';
$ano_publicacao = $_POST['ano_publicacao'] ?? '';
$cdd = $_POST['cdd'] ?? '';

if ($titulo === '' || $autor === '' || $ano_publicacao === '') {
    die('Todos os campos são obrigatórios');
}

$sql = "INSERT INTO livros (titulo, cdd, autor, ano_publicacao, status)
        VALUES (:titulo, :cdd, :autor, :ano_publicacao, 'disponivel')";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':titulo' => $titulo,
    ':cdd' => $cdd,
    ':autor' => $autor,
    ':ano_publicacao' => $ano_publicacao
]);

header('Location: listar_livros.php?sucesso=1');
exit;

