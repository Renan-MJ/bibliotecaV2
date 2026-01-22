<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<div class='container mt-5 text-center'><p class='alert alert-danger'>ID inválido.</p></div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}

$sql = "SELECT * FROM livros WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$livro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livro) {
    echo "<div class='container mt-5 text-center'><p class='alert alert-danger'>Livro não encontrado.</p></div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="listar_livros.php" class="text-decoration-none">Acervo</a></li>
                    <li class="breadcrumb-item active">Editar Registro</li>
                </ol>
            </nav>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white p-4" style="background-color: #0f172a !important;">
                    <h4 class="mb-0 fw-bold">
                        <i class="fa-solid fa-pen-to-square me-2 text-info"></i> Editar Livro
                    </h4>
                    <small class="text-slate-400">ID do Registro: #<?= $livro['id'] ?></small>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="atualizar_livro.php" method="post">
                        <input type="hidden" name="id" value="<?= $livro['id'] ?>">

                        <div class="row">
                            <div class="col-12 mb-4">
                                <label class="form-label fw-semibold text-muted">Título da Obra</label>
                                <div class="input-group shadow-sm border rounded-lg overflow-hidden">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-heading text-muted"></i></span>
                                    <input type="text" name="titulo" class="form-control border-0" value="<?= htmlspecialchars($livro['titulo']) ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-muted">Classificação CDD</label>
                                <div class="input-group shadow-sm border rounded-lg overflow-hidden">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-barcode text-muted"></i></span>
                                    <input type="text" name="cdd" class="form-control border-0" value="<?= htmlspecialchars($livro['cdd'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-muted">Status Atual</label>
                                <div class="input-group shadow-sm border rounded-lg overflow-hidden">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-circle-info text-muted"></i></span>
                                    <select name="status" class="form-select border-0">
                                        <option value="Disponível" <?= ($livro['status'] ?? '') == 'Disponível' ? 'selected' : '' ?>>Disponível</option>
                                        <option value="Emprestado" <?= ($livro['status'] ?? '') == 'Emprestado' ? 'selected' : '' ?>>Emprestado</option>
                                        <option value="Manutenção" <?= ($livro['status'] ?? '') == 'Manutenção' ? 'selected' : '' ?>>Manutenção</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <label class="form-label fw-semibold text-muted">Autor(a)</label>
                                <div class="input-group shadow-sm border rounded-lg overflow-hidden">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-user-pen text-muted"></i></span>
                                    <input type="text" name="autor" class="form-control border-0" value="<?= htmlspecialchars($livro['autor']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="listar_livros.php" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-2 text-danger"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow" style="background-color: #0f172a; border: none;">
                                <i class="fa-solid fa-rotate me-2 text-info"></i> Atualizar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-muted mt-4 small">
                Última modificação detectada no banco de dados para este ID.
            </p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>