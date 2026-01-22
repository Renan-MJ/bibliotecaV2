<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php'; // Integrando o cabeçalho moderno

$sql = "SELECT * FROM livros ORDER BY id DESC";
$stmt = $pdo->query($sql);
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Acervo Bibliográfico</h2>
            <p class="text-muted small">Visualize e gerencie os livros da biblioteca municipal.</p>
        </div>
        <a href="cadastrar_livro.php" class="btn btn-primary shadow-sm d-flex align-items-center gap-2" style="background-color: #0f172a; border: none;">
            <i class="fa-solid fa-plus"></i> Novo Livro
        </a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div>Livro cadastrado com sucesso no sistema!</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('sucesso');
                window.history.replaceState({}, document.title, url.pathname);
            }
        </script>
    <?php endif; ?>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Título da Obra</th>
                        <th class="py-3">Autor</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-secondary">
                    <?php if (count($livros) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open d-block mb-2 fa-2x opacity-25"></i>
                                Nenhum livro encontrado no acervo.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td class="ps-4 text-dark fw-medium">#<?= $livro['id'] ?></td>
                                <td>
                                    <span class="text-dark fw-semibold d-block"><?= htmlspecialchars($livro['titulo']) ?></span>
                                    <span class="text-muted small">Ref: CDD-<?= $livro['cdd'] ?? 'N/A' ?></span>
                                </td>
                                <td><?= htmlspecialchars($livro['autor']) ?></td>   
                                <td class="text-center">
                                    <?php 
                                        $statusClass = ($livro['STATUS'] == 'Disponível') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis';
                                    ?>
                                    <span class="badge rounded-pill <?= $statusClass ?> px-3">
                                        <?= $livro['STATUS'] ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="editar_livro.php?id=<?= $livro['id'] ?>" class="btn btn-white btn-sm border text-primary" title="Editar">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="excluir_livro.php" method="post" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este exemplar?');">
                                            <input type="hidden" name="id" value="<?= $livro['id'] ?>">
                                            <button type="submit" class="btn btn-white btn-sm border text-danger" title="Excluir">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>