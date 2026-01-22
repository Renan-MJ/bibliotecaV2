<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$mensagem_sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['sucesso']);

$data_hoje = date('Y-m-d');

$sql = "
SELECT e.id, l.id AS livro_id, l.titulo, le.id AS leitor_id, le.nome, le.numero_cadastro,
       e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real
FROM emprestimos e
JOIN livros l ON e.livro_id = l.id
JOIN leitores le ON e.leitor_id = le.id
ORDER BY e.id DESC
";
$emprestimos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listar Empréstimos</title>
</head>
<body>

<h1>Lista de Empréstimos</h1>

<?php if ($mensagem_sucesso): ?>
    <p style="color:green;"><?= $mensagem_sucesso ?></p>
<?php endif; ?>

<a href="cadastrar_emprestimo.php">Registrar Novo Empréstimo</a><br><br>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Código do Livro</th>
            <th>Título do Livro</th>
            <th>Número de Cadastro do Leitor</th>
            <th>Nome do Leitor</th>
            <th>Data do Empréstimo</th>
            <th>Prazo de Devolução</th>
            <th>Devolução Real</th>
            <th>Atrasado?</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($emprestimos as $e):
            $atrasado = (!$e['data_devolucao_real'] && $e['data_devolucao_prevista'] < $data_hoje);
        ?>
        <tr style="<?= $atrasado ? 'background-color:#fdd;' : '' ?>">
            <td><?= $e['livro_id'] ?></td>
            <td><?= htmlspecialchars($e['titulo']) ?></td>
            <td><?= $e['numero_cadastro'] ?></td>
            <td><?= htmlspecialchars($e['nome']) ?></td>
            <td><?= $e['data_emprestimo'] ?></td>
            <td><?= $e['data_devolucao_prevista'] ?></td>
            <td><?= $e['data_devolucao_real'] ?? '-' ?></td>
            <td><?= $atrasado ? 'Atrasado!' : '-' ?></td>
            <td>
                <?php if (!$e['data_devolucao_real']): ?>
                    <form action="registrar_devolucao.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                        <button type="submit">Registrar Devolução</button>
                    </form>
                <?php else: ?>
                    <span>Devolvido</span>
                <?php endif; ?>

                <a href="editar_emprestimo.php?id=<?= $e['id'] ?>">Editar</a>

                <form action="excluir_emprestimo.php" method="post" style="display:inline;" 
                      onsubmit="return confirm('Tem certeza que deseja excluir este empréstimo?');">
                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                    <button type="submit">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
