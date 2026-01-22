<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Livro</title>
</head>
<body>

    <h1>Cadastrar Livro</h1>

    <form action="salvar_livro.php" method="post">
        <label>
            Título:<br>
            <input type="text" name="titulo" required>
        </label><br><br>

        <label>
            CDD:<br>
            <input type="text" name="cdd" required>
        </label><br><br>

        <label>
            Autor:<br>
            <input type="text" name="autor" required>
        </label><br><br>

        <label>
            Ano de publicação:<br>
            <input type="number" name="ano_publicacao" required>
        </label><br><br>

        <button type="submit">Salvar</button>
    </form>

</body>
</html>
