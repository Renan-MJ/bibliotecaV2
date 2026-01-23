<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$mensagem_sucesso = $_SESSION['sucesso'] ?? '';
$mensagem_erro = $_SESSION['erro'] ?? '';
unset($_SESSION['sucesso'], $_SESSION['erro']);

// 2. Configurações de Paginação
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// 3. Lógica de Busca com Seletor
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$tipo_busca = isset($_GET['tipo_busca']) ? $_GET['tipo_busca'] : 'todos';

$condicoes = [];
$params = [];

if (!empty($busca)) {
    if ($tipo_busca === 'id' && is_numeric($busca)) {
        $condicoes[] = "id = :id_exato";
        $params[':id_exato'] = intval($busca);
    } elseif ($tipo_busca === 'titulo') {
        $condicoes[] = "titulo LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } elseif ($tipo_busca === 'autor') {
        $condicoes[] = "autor LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } elseif ($tipo_busca === 'registro') {
        $condicoes[] = "numero_registro LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } elseif ($tipo_busca === 'cdd') { // NOVA CATEGORIA CDD
        $condicoes[] = "cdd LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } else {
        // Opção "Todos" agora inclui o CDD explicitamente
        $condicoes[] = "(id = :id_exato OR titulo LIKE :busca OR autor LIKE :busca OR numero_registro LIKE :busca OR cdd LIKE :busca)";
        $params[':id_exato'] = is_numeric($busca) ? intval($busca) : 0;
        $params[':busca'] = '%' . $busca . '%';
    }
}

$sql_where = !empty($condicoes) ? " WHERE " . implode(" AND ", $condicoes) : "";

// 4. Consulta para contar o TOTAL
$sql_count = "SELECT COUNT(*) FROM livros" . $sql_where;
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $itens_por_pagina);

// 5. Consulta Principal
$sql = "SELECT * FROM livros" . $sql_where . " ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

foreach ($params as $chave => $valor) {
    $stmt->bindValue($chave, $valor);
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
                <select name="tipo_busca" class="form-select shadow-sm w-auto" onchange="this.form.submit()">
                    <option value="todos" <?= $tipo_busca == 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="id" <?= $tipo_busca == 'id' ? 'selected' : '' ?>>ID</option>
                    <option value="registro" <?= $tipo_busca == 'registro' ? 'selected' : '' ?>>Registro</option>
                    <option value="titulo" <?= $tipo_busca == 'titulo' ? 'selected' : '' ?>>Título</option>
                    <option value="autor" <?= $tipo_busca == 'autor' ? 'selected' : '' ?>>Autor</option>
                    <option value="cdd" <?= $tipo_busca == 'cdd' ? 'selected' : '' ?>>CDD</option>
                </select>
                
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="busca" id="inputBusca" class="form-control border-start-0" 
                           placeholder="Pesquisar..." 
                           value="<?= htmlspecialchars($busca) ?>" autocomplete="off">
                </div>

                <?php if (!empty($busca)): ?>
                    <a href="listar_livros.php" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2 fa-lg"></i>
            <?= $mensagem_sucesso ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($mensagem_erro): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2 fa-lg"></i>
            <?= $mensagem_erro ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    <?php if (count($livros) === 0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open d-block mb-2 fa-2x opacity-25"></i>
                                Nenhum livro encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-dark fw-bold">Reg: <?= htmlspecialchars($livro['numero_registro'] ?? 'N/A') ?></span><br>
                                    <small class="text-muted">ID: #<?= $livro['id'] ?></small>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold d-block"><?= htmlspecialchars($livro['titulo']) ?></span>
                                    <span class="text-muted small">Ref: CDD-<?= htmlspecialchars($livro['cdd'] ?? 'N/A') ?></span>
                                </td>
                                <td><?= htmlspecialchars($livro['autor']) ?></td>   
                                <td class="text-center">
                                    <?php 
                                    // CORREÇÃO DO STATUS: Verifica maiúsculo e minúsculo
                                    $status_raw = $livro['status'] ?? $livro['STATUS'] ?? 'Indisponível';
                                    $is_disponivel = (strcasecmp($status_raw, 'Disponível') == 0);
                                    $statusClass = $is_disponivel ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis'; 
                                    ?>
                                    <span class="badge rounded-pill <?= $statusClass ?> px-3"><?= htmlspecialchars($status_raw) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="editar_livro.php?id=<?= $livro['id'] ?>" class="btn btn-white btn-sm border text-primary" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <form action="excluir_livro.php" method="post" class="d-inline" onsubmit="return confirm('Tem certeza?');">
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
                        <a class="page-link text-dark" href="?pagina=<?= $pagina_atual - 1 ?>&busca=<?= urlencode($busca) ?>&tipo_busca=<?= $tipo_busca ?>">Anterior</a>
                    </li>
                    <?php for ($i = max(1, $pagina_atual - 2); $i <= min($total_paginas, $pagina_atual + 2); $i++): ?>
                        <li class="page-item <?= $pagina_atual == $i ? 'active' : '' ?>">
                            <a class="page-link <?= $pagina_atual == $i ? 'bg-dark border-dark text-white' : 'text-dark' ?>" href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>&tipo_busca=<?= $tipo_busca ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $pagina_atual >= $total_paginas ? 'disabled' : '' ?>">
                        <a class="page-link text-dark" href="?pagina=<?= $pagina_atual + 1 ?>&busca=<?= urlencode($busca) ?>&tipo_busca=<?= $tipo_busca ?>">Próximo</a>
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