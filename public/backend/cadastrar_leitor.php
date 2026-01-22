<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Leitor</title>
</head>
<body>
    <h1>Cadastrar Leitor</h1>

    <form action="salvar_leitor.php" method="post">
        <label>
            Número de cadastro:<br>
            <input type="number" name="numero_cadastro" required>
        </label><br><br>

        <label>
            Nome:<br>
            <input type="text" name="nome" required>
        </label><br><br>

        <label>
            Filiação:<br>
            <input type="text" name="filiacao">
        </label><br><br>

        <label>
            RG:<br>
            <input type="text" name="rg">
        </label><br><br>

        <label>
            Telefone:<br>
            <input type="text" name="telefone">
        </label><br><br>

        <label>
            Email:<br>
            <input type="email" name="email">
        </label><br><br>

        <label>
            Endereço:<br>
            <input type="text" name="endereco">
        </label><br><br>

        <button type="submit">Cadastrar</button>
        <a href="listar_leitores.php">Voltar</a>
    </form>
</body>
</html>
