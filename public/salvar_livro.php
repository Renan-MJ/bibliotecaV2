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

// Captura todos os campos necessários, incluindo o que estava causando erro
$numero_registro = trim($_POST['numero_registro'] ?? '');
$titulo          = trim($_POST['titulo'] ?? '');
$autor           = trim($_POST['autor'] ?? '');
$cdd             = trim($_POST['cdd'] ?? '');

// Validação (numero_registro agora é obrigatório para não dar erro no banco)
if (empty($numero_registro) || empty($titulo) || empty($autor)) {
    $_SESSION['erro'] = "Número de Registro, Título e Autor são obrigatórios.";
    header('Location: cadastrar_livro.php');
    exit;
}

try {
    // SQL atualizada com todas as colunas da sua tabela
    // Importante: Note que usei STATUS (em maiúsculo) para bater com sua descrição
    $sql = "INSERT INTO livros (numero_registro, titulo, cdd, autor, STATUS) 
            VALUES (:n_registro, :titulo, :cdd, :autor, 'Disponível')";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':n_registro' => $numero_registro,
        ':titulo'     => $titulo,
        ':cdd'        => $cdd,
        ':autor'      => $autor
    ]);

    // Sucesso! Limpa o buffer e vai para a listagem
    ob_end_clean();
    header('Location: listar_livros.php?sucesso=1');
    exit;

} catch (PDOException $e) {
    ob_end_clean();
    // Mensagem amigável para debug se algo mais falhar
    die("Erro Crítico no Banco de Dados: " . $e->getMessage());
}