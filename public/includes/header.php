<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0f172a; /* Azul Marinho da Prefeitura */
            --accent-color: #3b82f6;  /* Azul de destaque */
        }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        .navbar { background-color: var(--primary-color) !important; padding: 1rem 0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .nav-link { color: #cbd5e1 !important; font-weight: 500; transition: all 0.3s; margin: 0 10px; }
        .nav-link:hover { color: #ffffff !important; transform: translateY(-1px); }
        .nav-link.active { color: #ffffff !important; border-bottom: 2px solid var(--accent-color); }
        .navbar-brand { font-weight: 700; letter-spacing: -0.5px; }
        .user-profile { border-left: 1px solid #334155; padding-left: 20px; color: white; }
    </style>
</head>
<body>

<?php
// Função simples para marcar o link ativo
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fa-solid fa-landmark-flag me-2 text-info"></i>
            <span>BIBLIO<span class="text-info">V2</span></span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_atual == 'listar_livros.php' || $pagina_atual == 'cadastrar_livro.php') ? 'active' : '' ?>" href="listar_livros.php">
                        <i class="fa-solid fa-book me-1"></i> Acervo
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_atual == 'listar_leitores.php' || $pagina_atual == 'cadastrar_leitor.php') ? 'active' : '' ?>" href="listar_leitores.php">
                        <i class="fa-solid fa-users me-1"></i> Leitores
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_atual == 'listar_emprestimos.php' || $pagina_atual == 'cadastrar_emprestimo.php') ? 'active' : '' ?>" href="listar_emprestimos.php">
                        <i class="fa-solid fa-handshake me-1"></i> Empréstimos
                    </a>
                </li>
            </ul>
            
            <div class="user-profile d-none d-lg-flex align-items-center">
                <small class="me-3 text-white-50">Olá, <strong>Administrador</strong></small>
                <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Sair</a>
            </div>
        </div>
    </div>
</nav>