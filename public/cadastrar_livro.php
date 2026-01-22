<?php include_once __DIR__ . '/../includes/header.php'; ?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Livro</title>
    <!-- Link para o Bootstrap 5 via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Cadastrar Livro</h2>
            </div>
            <div class="card-body">
                <form action="salvar_livro.php" method="post">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título:</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="cdd" class="form-label">CDD:</label>
                        <input type="text" id="cdd" name="cdd" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="autor" class="form-label">Autor:</label>
                        <input type="text" id="autor" name="autor" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="ano_publicacao" class="form-label">Ano de publicação:</label>
                        <input type="number" id="ano_publicacao" name="ano_publicacao" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success">Salvar</button>
                    <a href="listar_livros.php" class="btn btn-secondary ms-2">Voltar</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Script do Bootstrap (opcional para componentes interativos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
