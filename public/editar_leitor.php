<?php
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die('ID inválido');
}

$sql = "SELECT * FROM leitores WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$leitor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leitor) {
    die('Leitor não encontrado');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Leitor</title>
</head>
<body>

<h1>Editar Leitor</h1>

<form action="atualizar_leitor.php" method="post">
    <input type="hidden" name="id" value="<?= $leitor['id'] ?>">

    <label>
        Número de cadastro:<br>
        <input type="number" name="numero_cadastro" value="<?= $leitor['numero_cadastro'] ?>" required>
    </label><br><br>

    <label>
        Nome:<br>
        <input type="text" name="nome" value="<?= htmlspecialchars($leitor['nome']) ?>" required>
    </label><br><br>

    <label>
        Filiação:<br>
        <input type="text" name="filiacao" value="<?= htmlspecialchars($leitor['filiacao']) ?>">
    </label><br><br>

    <label>
        RG:<br>
        <input type="text" name="rg" value="<?= htmlspecialchars($leitor['rg']) ?>">
    </label><br><br>

    <label>
        Telefone:<br>
        <input type="text" name="telefone" value="<?= htmlspecialchars($leitor['telefone']) ?>">
    </label><br><br>

    <label>
        Email:<br>
        <input type="email" name="email" value="<?= htmlspecialchars($leitor['email']) ?>">
    </label><br><br>

    <label>
        Endereço:<br>
        <input type="text" name="endereco" value="<?= htmlspecialchars($leitor['endereco']) ?>">
    </label><br><br>

    <button type="submit">Atualizar</button>
    <a href="listar_leitores.php">Cancelar</a>
</form>

</body>
</html>
