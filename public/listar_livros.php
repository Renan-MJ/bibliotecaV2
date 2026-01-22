<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

// 1. Configurações de Paginação
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// 2. Lógica de Busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$id_busca = is_numeric($busca) ? intval($busca) : 0;

// 3. Consulta para contar o TOTAL
if (!empty($busca)) {
    $sql_count = "SELECT COUNT(*) FROM livros 
                  WHERE id = :id_exato 
                  OR titulo LIKE :busca 
                  OR autor LIKE :busca 
                  OR numero_registro LIKE :busca 
                  OR cdd LIKE :busca";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->bindValue(':id_exato', $id_busca, PDO::PARAM_INT);
    $stmt_count->bindValue(':busca', '%' . $busca . '%', PDO::PARAM_STR);
    $stmt_count->execute();
} else {
    $sql_count = "SELECT COUNT(*) FROM livros";
    $stmt_count = $pdo->query($sql_count);
}
$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $itens_por_pagina);

// 4. Consulta Principal com LIMIT e OFFSET
if (!empty($busca)) {
    $sql = "SELECT * FROM livros 
            WHERE id = :id_exato 
            OR titulo LIKE :busca 
            OR autor LIKE :busca 
            OR numero_registro LIKE :busca 
            OR cdd LIKE :busca 
            ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_exato', $id_busca, PDO::PARAM_INT);
    $stmt->bindValue(':busca', '%' . $busca . '%', PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM livros ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
}

$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Acervo Bibliográfico</h2>
            <p class="text-muted small">Exibindo <?= count($livros) ?> de <?= $total_registros ?> livros.</p>
        </div>
        <a href="cadastrar_livro.php" class="btn btn-primary shadow-sm d-flex align-items-center gap-2" style="background-color: #0f172a; border: none;">
            <i class="fa-solid fa-plus"></i> Novo Livro
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="listar_livros.php" method="GET" id="formBusca" class="d-flex gap-2">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="busca" id="inputBusca" class="form-control border-start-0" 
                           placeholder="Buscar por ID, registro, título ou autor..." 
                           value="<?= htmlspecialchars($busca) ?>" autocomplete="off">
                </div>
                <?php if (!empty($busca)): ?>
                    <a href="listar_livros.php" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (!empty($busca) && count($livros) === 0): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            Nenhum livro encontrado para o termo: <strong>"<?= htmlspecialchars($busca) ?>"</strong>.
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">
                        <th class="ps-4 py-3">ID / Registro</th>
                        <th class="py-3">Título da Obra</th>
                        <th class="py-3">Autor</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-secondary">
                    <?php if (count($livros) === 0 && empty($busca)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open d-block mb-2 fa-2x opacity-25"></i>
                                O acervo está vazio.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <span class="text-dark fw-bold" style="font-size: 1rem;">
                                        Reg: <?= htmlspecialchars($livro['numero_registro'] ?? 'N/A') ?>
                                    </span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    ID: #<?= $livro['id'] ?>
                                </small>
                            </td>
                                <td>
                                    <span class="text-dark fw-semibold d-block"><?= htmlspecialchars($livro['titulo']) ?></span>
                                    <span class="text-muted small">Ref: CDD-<?= htmlspecialchars($livro['cdd'] ?? 'N/A') ?></span>
                                </td>
                                <td><?= htmlspecialchars($livro['autor']) ?></td>   
                                <td class="text-center">
                                    <?php $statusClass = ($livro['STATUS'] == 'Disponível') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis'; ?>
                                    <span class="badge rounded-pill <?= $statusClass ?> px-3"><?= $livro['STATUS'] ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="editar_livro.php?id=<?= $livro['id'] ?>" class="btn btn-white btn-sm border text-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <form action="excluir_livro.php" method="post" class="d-inline" onsubmit="return confirm('Deseja excluir?');">
                                            <input type="hidden" name="id" value="<?= $livro['id'] ?>">
                                            <button type="submit" class="btn btn-white btn-sm border text-danger"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_paginas > 1): ?>
        <div class="card-footer bg-white py-3">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $pagina_atual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link text-dark" href="?pagina=<?= $pagina_atual - 1 ?>&busca=<?= urlencode($busca) ?>">Anterior</a>
                    </li>
                    
                    <?php 
                    $inicio = max(1, $pagina_atual - 2);
                    $fim = min($total_paginas, $pagina_atual + 2);
                    for ($i = $inicio; $i <= $fim; $i++): ?>
                        <li class="page-item <?= $pagina_atual == $i ? 'active' : '' ?>">
                            <a class="page-link <?= $pagina_atual == $i ? 'bg-dark border-dark text-white' : 'text-dark' ?>" href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $pagina_atual >= $total_paginas ? 'disabled' : '' ?>">
                        <a class="page-link text-dark" href="?pagina=<?= $pagina_atual + 1 ?>&busca=<?= urlencode($busca) ?>">Próximo</a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const inputBusca = document.getElementById('inputBusca');
    const formBusca = document.getElementById('formBusca');
    let tempoEspera;

    inputBusca.addEventListener('input', () => {
        clearTimeout(tempoEspera);
        tempoEspera = setTimeout(() => {
            formBusca.submit();
        }, 500);
    });

    // Mantém o foco no final do input após o refresh
    if (inputBusca.value !== "") {
        const val = inputBusca.value;
        inputBusca.focus();
        inputBusca.setSelectionRange(val.length, val.length);
    }
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>