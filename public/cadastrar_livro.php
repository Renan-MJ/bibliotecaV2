<?php 
include_once __DIR__ . '/includes/header.php'; 
?>

<style>
    /* Estilos que respeitam o tema Dark/Light automaticamente */
    .card-custom {
        border: none;
        border-radius: 12px;
        background-color: var(--bs-card-bg);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .card-header-auth {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border-radius: 12px 12px 0 0 !important;
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        text-uppercase;
        letter-spacing: 0.5px;
        color: var(--bs-secondary-color);
    }

    .input-group-text {
        background-color: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color);
        color: var(--bs-secondary-color);
    }

    .form-control {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-color: var(--bs-border-color);
        padding: 0.75rem;
    }

    .form-control:focus {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
    }

    .btn-save {
        background-color: #059669;
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-save:hover {
        background-color: #047857;
        transform: translateY(-1px);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="listar_livros.php" class="text-decoration-none">Acervo</a></li>
                    <li class="breadcrumb-item active">Novo Cadastro</li>
                </ol>
            </nav>

            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <?= $_SESSION['erro']; unset($_SESSION['erro']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card card-custom shadow-lg">
                <div class="card-header-auth d-flex align-items-center">
                    <i class="fa-solid fa-book-medical me-3 fa-lg text-info"></i>
                    <h4 class="mb-0 h5">Cadastrar Novo Livro</h4>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="salvar_livro.php" method="post">
                        
                        <div class="mb-4">
                            <label for="numero_registro" class="form-label">Número de Registro</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                                <input type="text" id="numero_registro" name="numero_registro" class="form-control" placeholder="Ex: 20240001" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="titulo" class="form-label">Título da Obra</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-heading"></i></span>
                                <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Ex: Dom Casmurro" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="cdd" class="form-label">Código CDD (Classificação)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
                                <input type="text" id="cdd" name="cdd" class="form-control" placeholder="Ex: 869.3" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="autor" class="form-label">Autor(a)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-pen"></i></span>
                                <input type="text" id="autor" name="autor" class="form-control" placeholder="Nome completo do autor" required>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="listar_livros.php" class="btn btn-outline-secondary border-0">
                                <i class="fa-solid fa-xmark me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-save text-white px-4 shadow-sm">
                                <i class="fa-solid fa-check me-2"></i> Salvar Livro
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-4 mb-0 small opacity-75">
                Prefeitura Municipal - Sistema Interno de Gestão de Acervo
            </p>

        </div>
    </div>
</div>

<?php 
include_once __DIR__ . '/includes/footer.php'; 
?>