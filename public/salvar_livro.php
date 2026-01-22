<?php
// 1. Caminho para a configuração do banco
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// 2. Garante que não haja saída de texto antes do redirecionamento
ob_start(); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_livros.php');
    exit;
}

// Captura todos os campos necessários
$numero_registro = trim($_POST['numero_registro'] ?? '');
$titulo          = trim($_POST['titulo'] ?? '');
$autor           = trim($_POST['autor'] ?? '');
$cdd             = trim($_POST['cdd'] ?? '');

// Validação
if (empty($numero_registro) || empty($titulo) || empty($autor)) {
    $_SESSION['erro'] = "Número de Registro, Título e Autor são obrigatórios.";
    header('Location: cadastrar_livro.php');
    exit;
}

try {
    // SQL atualizada
    $sql = "INSERT INTO livros (numero_registro, titulo, cdd, autor, STATUS) 
            VALUES (:n_registro, :titulo, :cdd, :autor, 'Disponível')";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':n_registro' => $numero_registro,
        ':titulo'     => $titulo,
        ':cdd'        => $cdd,
        ':autor'      => $autor
    ]);
     
    // --- ALTERAÇÃO AQUI ---
    // Define a mensagem de sucesso na sessão antes de redirecionar
    $_SESSION['sucesso'] = "O exemplar <strong>" . htmlspecialchars($titulo) . "</strong> foi cadastrado com sucesso no acervo!";

    ob_end_clean();
    header('Location: listar_livros.php'); // Removido o ?sucesso=1
    exit;

} catch (PDOException $e) {
    ob_end_clean();
    die("Erro Crítico no Banco de Dados: " . $e->getMessage());
}