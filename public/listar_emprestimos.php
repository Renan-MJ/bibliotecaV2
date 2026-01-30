<?php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$mensagem_sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['sucesso']);

require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';


$data_hoje = date('Y-m-d');

// 1. Configurações de Paginação
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// 2. Lógica de Busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// 3. Consulta para contar o TOTAL (com os JOINs necessários para o filtro)
$sql_count = "SELECT COUNT(*) FROM emprestimos e
              JOIN livros l ON e.livro_id = l.id
              JOIN leitores le ON e.leitor_id = le.id";

if (!empty($busca)) {
    $sql_count .= " WHERE l.titulo LIKE :busca 
                    OR le.nome LIKE :busca 
                    OR le.numero_cadastro LIKE :busca 
                    OR l.id = :id_busca";
}

$stmt_count = $pdo->prepare($sql_count);
if (!empty($busca)) {
    $stmt_count->bindValue(':busca', '%' . $busca . '%', PDO::PARAM_STR);
    $stmt_count->bindValue(':id_busca', is_numeric($busca) ? $busca : 0, PDO::PARAM_INT);
}
$stmt_count->execute();
$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $itens_por_pagina);

// 4. Consulta Principal com LIMIT, OFFSET e FILTRO
$sql = "SELECT e.id, l.id AS livro_id, l.titulo, le.id AS leitor_id, le.nome, le.numero_cadastro,
               e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real
        FROM emprestimos e
        JOIN livros l ON e.livro_id = l.id
        JOIN leitores le ON e.leitor_id = le.id";

if (!empty($busca)) {
    $sql .= " WHERE l.titulo LIKE :busca 
               OR le.nome LIKE :busca 
               OR le.numero_cadastro LIKE :busca 
               OR l.id = :id_busca";
}

$sql .= " ORDER BY e.data_devolucao_real ASC, e.data_devolucao_prevista ASC 
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if (!empty($busca)) {
    $stmt->bindValue(':busca', '%' . $busca . '%', PDO::PARAM_STR);
    $stmt->bindValue(':id_busca', is_numeric($busca) ? $busca : 0, PDO::PARAM_INT);
}
$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Controle de Empréstimos</h2>
            <p class="text-muted small">Gerencie prazos, devoluções e alertas de atraso.</p>
        </div>
        <a href="cadastrar_emprestimo.php" class="btn btn-primary shadow-sm" style="background-color: #0f172a; border: none;">
            <i class="fa-solid fa-handshake-angle me-2"></i> Novo Empréstimo
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="listar_emprestimos.php" method="GET" id="formBusca" class="d-flex gap-2">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="busca" id="inputBusca" class="form-control border-start-0" 
                           placeholder="Buscar por leitor, livro ou matrícula..." 
                           value="<?= htmlspecialchars($busca) ?>" autocomplete="off">
                </div>
                <?php if (!empty($busca)): ?>
                    <a href="listar_emprestimos.php" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> <?= $mensagem_sucesso ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Livro</th>
                        <th class="py-3">Leitor</th>
                        <th class="py-3">Datas (Saída/Prazo)</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-secondary">
                    <?php if (empty($emprestimos)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Nenhum registro encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($emprestimos as $e): 
                            $devolvido = !empty($e['data_devolucao_real']);
                            $atrasado = (!$devolvido && $e['data_devolucao_prevista'] < $data_hoje);
                            
                            if ($devolvido) {
                                $badgeClass = "bg-secondary-subtle text-secondary";
                                $statusLabel = "Devolvido";
                                $rowClass = "opacity-75";
                            } elseif ($atrasado) {
                                $badgeClass = "bg-danger-subtle text-danger";
                                $statusLabel = "Atrasado";
                                $rowClass = "table-danger";
                            } else {
                                $badgeClass = "bg-info-subtle text-info-emphasis";
                                $statusLabel = "Em aberto";
                                $rowClass = "";
                            }
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td class="ps-4">
                                <span class="text-dark fw-bold d-block"><?= htmlspecialchars($e['titulo']) ?></span>
                                <span class="text-muted small">ID Livro: #<?= $e['livro_id'] ?></span>
                            </td>
                            <td>
                                <span class="text-dark d-block"><?= htmlspecialchars($e['nome']) ?></span>
                                <span class="text-muted small">Nº Cadastro: <?= $e['numero_cadastro'] ?></span>
                            </td>
                            <td class="small">
                                <div><i class="fa-solid fa-calendar-minus me-1 text-muted"></i> <?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></div>
                                <div class="fw-bold"><i class="fa-solid fa-calendar-check me-1 text-muted"></i> <?= date('d/m/Y', strtotime($e['data_devolucao_prevista'])) ?></div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-2 text-uppercase" style="font-size: 0.65rem;">
                                    <?= $statusLabel ?>
                                </span>
                                <?php if($devolvido): ?>
                                    <div class="text-muted" style="font-size: 0.7rem;">Em: <?= date('d/m/Y', strtotime($e['data_devolucao_real'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm">
                                    <?php if (!$devolvido): ?>
                                        <form action="registrar_devolucao.php" method="post" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-white border text-success" title="Registrar Devolução">
                                                <i class="fa-solid fa-arrow-rotate-left"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="editar_emprestimo.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-white border text-primary" title="Editar Datas">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form action="excluir_emprestimo.php" method="post" class="d-inline" onsubmit="return confirm('Excluir este registro permanentemente?');">
                                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-white border text-danger">
                                            <i class="fa-solid fa-trash"></i>
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
                    for ($i = $inicio; $i <= $fim; $i++): ?>
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

    if (inputBusca.value !== "") {
        const val = inputBusca.value;
        inputBusca.focus();
        inputBusca.setSelectionRange(val.length, val.length);
    }
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>