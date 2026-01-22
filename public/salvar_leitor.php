<?php
require_once __DIR__ . '/../config/database.php';

// Inicia a sessão no topo para garantir que as mensagens funcionem
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_leitores.php');
    exit;
}

// Captura e limpeza básica de dados
$numero_cadastro = $_POST['numero_cadastro'] ?? '';
$nome            = $_POST['nome'] ?? '';
$filiacao        = $_POST['filiacao'] ?? '';
$rg              = $_POST['rg'] ?? '';
$telefone        = $_POST['telefone'] ?? '';
$email           = $_POST['email'] ?? '';
$endereco        = $_POST['endereco'] ?? '';

// Validação de campos obrigatórios
if (empty($numero_cadastro) || empty($nome)) {
    $_SESSION['erro'] = "Os campos <strong>Número de Cadastro</strong> e <strong>Nome</strong> são obrigatórios.";
    header('Location: cadastrar_leitor.php');
    exit;
}

try {
    // SQL alterado: trocamos CURDATE() por NOW() para capturar o horário exato
    $sql = "INSERT INTO leitores (numero_cadastro, nome, filiacao, rg, telefone, email, endereco, data_cadastro)
            VALUES (:numero_cadastro, :nome, :filiacao, :rg, :telefone, :email, :endereco, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':numero_cadastro' => $numero_cadastro,
        ':nome'            => $nome,
        ':filiacao'        => $filiacao,
        ':rg'              => $rg,
        ':telefone'        => $telefone,
        ':email'           => $email,
        ':endereco'        => $endereco
    ]);

    // Mensagem de sucesso estilizada
    $_SESSION['sucesso'] = "O cidadão <strong>" . htmlspecialchars($nome) . "</strong> foi cadastrado com sucesso no sistema!";
    header('Location: listar_leitores.php');
    exit;

} catch (PDOException $e) {
    // Caso o erro seja de coluna não encontrada, certifique-se de ter rodado o ALTER TABLE enviado antes
    $_SESSION['erro'] = "Erro ao realizar o cadastro. Detalhes: " . $e->getMessage();
    header('Location: cadastrar_leitor.php');
    exit;
}