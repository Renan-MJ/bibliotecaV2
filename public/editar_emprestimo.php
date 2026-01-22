<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) die('ID inválido');

$sql = "SELECT * FROM emprestimos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$emprestimo) die('Empréstimo não encontrado');

$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);
$leitores = $pdo->query("SELECT id, nome, numero_cadastro FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Empréstimo</title>
</head>
<body>

<h1>Editar Empréstimo</h1>

<form action="atualizar_emprestimo.php" method="post">
    <input type="hidden" name="id" value="<?= $emprestimo['id'] ?>">

    <label>
        Livro:<br>
        <select name="livro_id" required>
            <?php foreach ($livros as $livro): ?>
                <option value="<?= $livro['id'] ?>" <?= $livro['id'] == $emprestimo['livro_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($livro['titulo']) ?> (ID: <?= $livro['id'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>
        Leitor:<br>
        <select name="leitor_id" required>
            <?php foreach ($leitores as $leitor): ?>
                <option value="<?= $leitor['id'] ?>" <?= $leitor['id'] == $emprestimo['leitor_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($leitor['nome']) ?> (Nº Cadastro: <?= $leitor['numero_cadastro'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>
        Data do empréstimo:<br>
        <input type="date" name="data_emprestimo" value="<?= $emprestimo['data_emprestimo'] ?>" required>
    </label><br><br>

    <label>
        Data prevista de devolução:<br>
        <input type="date" name="data_devolucao_prevista" value="<?= $emprestimo['data_devolucao_prevista'] ?>" required>
    </label><br><br>

    <label>
        Data de devolução real (opcional):<br>
        <input type="date" name="data_devolucao_real" value="<?= $emprestimo['data_devolucao_real'] ?>">
    </label><br><br>

    <button type="submit">Atualizar Empréstimo</button>
    <a href="listar_emprestimos.php">Cancelar</a>
</form>

</body>
</html>
