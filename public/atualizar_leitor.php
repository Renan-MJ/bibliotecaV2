<?php
require_once __DIR__ . '/../config/database.php';

// Inicia a sessão para podermos enviar a mensagem de sucesso/erro
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_leitores.php');
    exit;
}

// Captura de dados com tratamento básico
$id              = $_POST['id'] ?? null;
$numero_cadastro = $_POST['numero_cadastro'] ?? '';
$nome            = $_POST['nome'] ?? '';
$filiacao        = $_POST['filiacao'] ?? '';
$rg              = $_POST['rg'] ?? '';
$telefone        = $_POST['telefone'] ?? '';
$email           = $_POST['email'] ?? '';
$endereco        = $_POST['endereco'] ?? '';

// Validação de segurança
if (!$id || empty($numero_cadastro) || empty($nome)) {
    $_SESSION['erro'] = "Preencha os campos obrigatórios para atualizar o cadastro.";
    header('Location: listar_leitores.php');
    exit;
}

try {
    // SQL organizada e segura
    $sql = "UPDATE leitores 
            SET numero_cadastro = :numero_cadastro, 
                nome = :nome, 
                filiacao = :filiacao,
                rg = :rg, 
                telefone = :telefone, 
                email = :email, 
                endereco = :endereco
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':numero_cadastro' => $numero_cadastro,
        ':nome'            => $nome,
        ':filiacao'        => $filiacao,
        ':rg'              => $rg,
        ':telefone'        => $telefone,
        ':email'           => $email,
        ':endereco'        => $endereco,
        ':id'              => $id
    ]);

    // Mensagem de sucesso profissional
    $_SESSION['sucesso'] = "O cadastro de <strong>" . htmlspecialchars($nome) . "</strong> foi atualizado com sucesso!";
    header('Location: listar_leitores.php');
    exit;

} catch (PDOException $e) {
    // Tratamento de erro caso o banco de dados falhe
    $_SESSION['erro'] = "Erro técnico ao atualizar o leitor. Por favor, contate o suporte.";
    // Opcional: logar o erro $e->getMessage() para o desenvolvedor
    header('Location: listar_leitores.php');
    exit;
}