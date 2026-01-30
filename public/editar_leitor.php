<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<div class='container mt-5'><p class='alert alert-danger shadow-sm'>ID inválido.</p></div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}

$sql = "SELECT * FROM leitores WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$leitor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leitor) {
    echo "<div class='container mt-5'><p class='alert alert-danger shadow-sm'>Leitor não encontrado.</p></div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="listar_leitores.php" class="text-decoration-none text-muted">Leitores</a></li>
                    <li class="breadcrumb-item active fw-bold" aria-current="page">Editar Cadastro</li>
                </ol>
            </nav>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white p-4" style="background-color: #0f172a !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold"><i class="fa-solid fa-user-pen me-2 text-info"></i> Editar Perfil do Leitor</h4>
                        <span class="badge bg-secondary">ID: #<?= $leitor['id'] ?></span>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white"> 
                    <form action="atualizar_leitor.php" method="post">
                        <input type="hidden" name="id" value="<?= $leitor['id'] ?>">

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Nº de Cadastro</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-id-card-clip text-muted"></i></span>
                                    <input type="number" name="numero_cadastro" class="form-control border-start-0 ps-0" value="<?= $leitor['numero_cadastro'] ?>" required>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold text-muted small text-uppercase">Nome Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                                    <input type="text" name="nome" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($leitor['nome']) ?>" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase">Data de Nascimento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-calendar-day text-muted"></i></span>
                                    <input type="date" name="data_nascimento" class="form-control border-start-0 ps-0" value="<?= $leitor['data_nascimento'] ?>">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase">Filiação</label>
                                <input type="text" name="filiacao" class="form-control bg-light-subtle" value="<?= htmlspecialchars($leitor['filiacao']) ?>" placeholder="Nome dos pais ou responsáveis">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">RG</label>
                                <input type="text" name="rg" class="form-control" value="<?= htmlspecialchars($leitor['rg']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($leitor['telefone']) ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">E-mail</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($leitor['email']) ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase">Endereço Residencial</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-location-dot"></i></span>
                                    <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($leitor['endereco']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="hr-text text-muted my-5"><hr></div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="listar_leitores.php" class="btn btn-outline-secondary px-4 py-2 fw-semibold">
                                <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="background-color: #0f172a; border: none;">
                                <i class="fa-solid fa-rotate me-2 text-info"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light py-3 text-center">
                    <small class="text-muted">Certifique-se de conferir os documentos originais antes de atualizar os dados do cidadão.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>