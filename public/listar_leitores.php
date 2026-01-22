<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$mensagem_sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['sucesso']);

// 1. Configurações de Paginação
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// 2. Lógica de Busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// 3. Consulta para contar o TOTAL
if (!empty($busca)) {
    $sql_count = "SELECT COUNT(*) FROM leitores 
                  WHERE numero_cadastro LIKE :busca 
                  OR nome LIKE :busca 
                  OR rg LIKE :busca 
                  OR email LIKE :busca 
                  OR telefone LIKE :busca";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->bindValue(':busca', '%' . $busca . '%', PDO::PARAM_STR);
    $stmt_count->execute();
} else {
    $sql_count = "SELECT COUNT(*) FROM leitores";
    $stmt_count = $pdo->query($sql_count);
}

$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $itens_por_pagina);

// 4. Consulta Principal com LIMIT e OFFSET
if (!empty($busca)) {
    $sql = "SELECT * FROM leitores 
            WHERE numero_cadastro LIKE :busca 
            OR nome LIKE :busca 
            OR rg LIKE :busca 
            OR email LIKE :busca 
            OR telefone LIKE :busca 
            ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':busca', '%' . $busca . '%', PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM leitores ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
}

$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$leitores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Gestão de Leitores</h2>
            <p class="text-muted small">Exibindo <?= count($leitores) ?> de <?= $total_registros ?> leitores.</p>
        </div>
        <a href="cadastrar_leitor.php" class="btn btn-primary shadow-sm d-flex align-items-center gap-2" style="background-color: #0f172a; border: none;">
            <i class="fa-solid fa-user-plus"></i> Novo Leitor
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="listar_leitores.php" method="GET" id="formBusca" class="d-flex gap-2">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="busca" id="inputBusca" class="form-control border-start-0" 
                           placeholder="Buscar por Nº cadastro, nome, RG, email ou telefone..." 
                           value="<?= htmlspecialchars($busca) ?>" autocomplete="off">
                </div>
                <?php if (!empty($busca)): ?>
                    <a href="listar_leitores.php" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div><?= $mensagem_sucesso ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($busca) && count($leitores) === 0): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            Nenhum leitor encontrado para: <strong>"<?= htmlspecialchars($busca) ?>"</strong>.
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3" style="width: 150px;">Nº Cadastro</th>
                        <th class="py-3">Cidadão / Filiação</th>
                        <th class="py-3">Documentação / Contato</th>
                        <th class="py-3">Endereço</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-secondary">
                    <?php if (count($leitores) === 0 && empty($busca)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">Nenhum leitor cadastrado no sistema.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leitores as $leitor): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <span class="badge bg-light text-dark border">#<?= $leitor['numero_cadastro'] ?></span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block"><?= htmlspecialchars($leitor['nome']) ?></span>
                                    <span class="text-muted small">Pai/Mãe: <?= htmlspecialchars($leitor['filiacao']) ?></span>
                                </td>
                                <td>
                                    <div class="small"><i class="fa-solid fa-address-card me-1 opacity-50"></i> RG: <?= htmlspecialchars($leitor['rg']) ?></div>
                                    <div class="small text-truncate" style="max-width: 180px;"><i class="fa-solid fa-envelope me-1 opacity-50"></i> <?= htmlspecialchars($leitor['email']) ?></div>
                                    <div class="small"><i class="fa-solid fa-phone me-1 opacity-50"></i> <?= htmlspecialchars($leitor['telefone']) ?></div>
                                </td>
                                <td class="small">
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($leitor['endereco']) ?>">
                                        <i class="fa-solid fa-location-dot me-1 text-muted"></i> <?= htmlspecialchars($leitor['endereco']) ?>
                                    </span>
                                    <div class="text-muted" style="font-size: 0.7rem;">Cadastrado em: <?= date('d/m/Y', strtotime($leitor['data_cadastro'])) ?></div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="editar_leitor.php?id=<?= $leitor['id'] ?>" class="btn btn-white btn-sm border text-primary" title="Editar">
                                            <i class="fa-solid fa-user-pen"></i>
                                        </a>
                                        <form action="excluir_leitor.php" method="post" class="d-inline" 
                                              onsubmit="return confirm('Deseja excluir permanentemente este leitor?');">
                                            <input type="hidden" name="id" value="<?= $leitor['id'] ?>">
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

        <?php if ($total_paginas > 1): ?>
        <div class="card-footer bg-white py-3 border-top-0">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $pagina_atual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link text-dark" href="?pagina=<?= $pagina_atual - 1 ?>&busca=<?= urlencode($busca) ?>">Anterior</a>
                    </li>
                    
                    <?php 
                    $inicio = max(1, $pagina_atual - 2);
                    $fim = min($total_paginas, $pagina_atual + 2);
                    for ($i = $inicio; $i <= $fim; $i++): 
                    ?>
                        <li class="page-item <?= $pagina_atual == $i ? 'active' : '' ?>">
                            <a class="page-link <?= $pagina_atual == $i ? 'bg-dark border-dark text-white' : 'text-dark' ?>" 
                               href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>"><?= $i ?></a>
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

    // Mantém o foco e cursor no final
    if (inputBusca.value !== "") {
        const length = inputBusca.value.length;
        inputBusca.focus();
        inputBusca.setSelectionRange(length, length);
    }
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>