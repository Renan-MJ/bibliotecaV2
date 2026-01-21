<?php
require_once __DIR__ . '/../config/database.php';

$sql = "SELECT * FROM livros ORDER BY id DESC";
$stmt = $pdo->query($sql);
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Livros</title>
</head>
<body>

    <h1>Livros Cadastrados</h1>

        <?php if (isset($_GET['sucesso'])): ?>
            <p style="color: green;">Livro cadastrado com sucesso!</p>

            <script>
                // Remove o parâmetro ?sucesso da URL sem recarregar a página
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('sucesso');
                    window.history.replaceState({}, document.title, url.pathname);
                }
            </script>
        <?php endif; ?>


    <a href="cadastrar_livro.php">Cadastrar novo livro</a>
    <br><br>

    <?php if (count($livros) === 0): ?>
        <p>Nenhum livro cadastrado.</p>
    <?php else: ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Ano</th>
                <th>Status</th>
            </tr>

            <?php foreach ($livros as $livro): ?>
                <tr>
                    <td><?= $livro['id'] ?></td>
                    <td><?= htmlspecialchars($livro['titulo']) ?></td>
                    <td><?= htmlspecialchars($livro['autor']) ?></td>
                    <td><?= $livro['ano_publicacao'] ?></td>
                    <td><?= $livro['status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</body>
</html>
