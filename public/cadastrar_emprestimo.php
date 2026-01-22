<?php
require_once __DIR__ . '/../config/database.php';

// Pega todos os livros
$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);

// Pega todos os leitores
$leitores = $pdo->query("SELECT id, nome, numero_cadastro FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Empréstimo</title>
</head>
<body>

<h1>Cadastrar Empréstimo</h1>

<form action="salvar_emprestimo.php" method="post">
    <label>
        Livro:<br>
        <select name="livro_id" required>
            <option value="">Selecione um livro</option>
            <?php foreach ($livros as $livro): ?>
                <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?> (ID: <?= $livro['id'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>
        Leitor:<br>
        <select name="leitor_id" required>
            <option value="">Selecione um leitor</option>
            <?php foreach ($leitores as $leitor): ?>
                <option value="<?= $leitor['id'] ?>"><?= htmlspecialchars($leitor['nome']) ?> (Nº Cadastro: <?= $leitor['numero_cadastro'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>
        Data do empréstimo:<br>
        <input type="date" name="data_emprestimo" value="<?= date('Y-m-d') ?>" required>
    </label><br><br>

    <label>
        Data prevista de devolução:<br>
        <input type="date" name="data_devolucao_prevista" required>
    </label><br><br>

    <button type="submit">Registrar Empréstimo</button>
    <a href="listar_emprestimos.php">Voltar</a>
</form>

</body>
</html>
