<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$id = $_POST['id'] ?? null;
if (!$id) die('ID inválido');

$sql = "DELETE FROM emprestimos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

$_SESSION['sucesso'] = 'Empréstimo excluído com sucesso!';
header('Location: listar_emprestimos.php');
exit;
