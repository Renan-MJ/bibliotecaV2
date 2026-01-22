<?php
require_once __DIR__ . '/../config/database.php';

// Mensagem de sucesso
session_start();

// Verifica se existe mensagem de sucesso na sessão
$mensagem_sucesso = $_SESSION['sucesso'] ?? '';

// Limpa a mensagem da sessão para não repetir no reload
unset($_SESSION['sucesso']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listar Leitores</title>
</head>
<body>

<h1>Lista de Leitores</h1>

<?php if ($mensagem_sucesso): ?>
    <p style="color:green;"><?= $mensagem_sucesso ?></p>
<?php endif; ?>

<a href="cadastrar_leitor.php">Cadastrar Novo Leitor</a><br><br>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Número de Cadastro</th>
            <th>Nome</th>
            <th>Filiação</th>
            <th>RG</th>
            <th>Telefone</th>
            <th>Email</th>
            <th>Endereço</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM leitores ORDER BY id DESC";
        $stmt = $pdo->query($sql);
        $leitores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($leitores as $leitor):
        ?>
        <tr>
            <td><?= $leitor['numero_cadastro'] ?></td>
            <td><?= htmlspecialchars($leitor['nome']) ?></td>
            <td><?= htmlspecialchars($leitor['filiacao']) ?></td>
            <td><?= htmlspecialchars($leitor['rg']) ?></td>
            <td><?= htmlspecialchars($leitor['telefone']) ?></td>
            <td><?= htmlspecialchars($leitor['email']) ?></td>
            <td><?= htmlspecialchars($leitor['endereco']) ?></td>
            <td>
                <a href="editar_leitor.php?id=<?= $leitor['id'] ?>">Editar</a>

                <form action="excluir_leitor.php" method="post" style="display:inline;" 
                      onsubmit="return confirm('Tem certeza que deseja excluir este leitor?');">
                    <input type="hidden" name="id" value="<?= $leitor['id'] ?>">
                    <button type="submit">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
