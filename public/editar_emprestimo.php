<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='container mt-5 alert alert-danger'>ID inválido.</div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}

// Busca os dados do empréstimo atual
$sql = "SELECT * FROM emprestimos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$emprestimo) {
    echo "<div class='container mt-5 alert alert-danger'>Empréstimo não encontrado.</div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}

// Pega todos os livros (independente de status para permitir manter o atual)
$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);
$leitores = $pdo->query("SELECT id, nome, numero_cadastro FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="listar_emprestimos.php" class="text-decoration-none">Empréstimos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Registro</li>
                </ol>
            </nav>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white p-4" style="background-color: #0f172a !important;">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2 text-info"></i> Ajustar Protocolo de Empréstimo</h4>
                    <small class="opacity-75">ID do Registro: #<?= $emprestimo['id'] ?></small>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="atualizar_emprestimo.php" method="post">
                        <input type="hidden" name="id" value="<?= $emprestimo['id'] ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Livro Associado</label>
                            <select name="livro_id" class="form-select border-primary-subtle" required>
                                <?php foreach ($livros as $livro): ?>
                                    <option value="<?= $livro['id'] ?>" <?= $livro['id'] == $emprestimo['livro_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($livro['titulo']) ?> (ID: <?= $livro['id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Leitor Responsável</label>
                            <select name="leitor_id" class="form-select" required>
                                <?php foreach ($leitores as $leitor): ?>
                                    <option value="<?= $leitor['id'] ?>" <?= $leitor['id'] == $emprestimo['leitor_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($leitor['nome']) ?> (Nº: <?= $leitor['numero_cadastro'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Data de Saída</label>
                                <input type="date" 
                                        name="data_emprestimo" 
                                        class="form-control bg-light" 
                                        value="<?= date('Y-m-d', strtotime($emprestimo['data_emprestimo'])) ?>" 
                                        readonly>
                                    <div class="form-text text-muted">A data de saída original não pode ser alterada.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Prazo Previsto</label>
                                <input type="date" name="data_devolucao_prevista" class="form-control" value="<?= $emprestimo['data_devolucao_prevista'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase text-primary">Devolução Real</label>
                                <input type="date" name="data_devolucao_real" class="form-control border-primary" value="<?= $emprestimo['data_devolucao_real'] ?>">
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded mb-4">
                            <small class="text-muted d-block">
                                <i class="fa-solid fa-circle-info me-1"></i> 
                                Se você preencher a <strong>Devolução Real</strong>, o livro voltará automaticamente para o status "Disponível" ao salvar.
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <a href="listar_emprestimos.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="background-color: #0f172a; border: none;">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Atualizar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>