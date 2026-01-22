<?php
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die('ID inválido');
}

$sql = "SELECT * FROM livros WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$livro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livro) {
    die('Livro não encontrado');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Livro</title>
</head>
<body>

    <h1>Editar Livro</h1>

    <form action="atualizar_livro.php" method="post">
        <input type="hidden" name="id" value="<?= $livro['id'] ?>">

        <label>
            Título:<br>
            <input type="text" name="titulo" value="<?= htmlspecialchars($livro['titulo']) ?>" required>
        </label><br><br>

        <label>
            Autor:<br>
            <input type="text" name="autor" value="<?= htmlspecialchars($livro['autor']) ?>" required>
        </label><br><br>

        <label>
            Ano de publicação:<br>
            <input type="number" name="ano_publicacao" value="<?= $livro['ano_publicacao'] ?>" required>
        </label><br><br>

        <button type="submit">Atualizar</button>
        <a href="listar_livros.php">Cancelar</a>
    </form>

</body>
</html>
