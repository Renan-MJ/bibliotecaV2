<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

// 1. Inicia a sessão e captura mensagens
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$mensagem_sucesso = $_SESSION['sucesso'] ?? '';
$mensagem_erro = $_SESSION['erro'] ?? '';
unset($_SESSION['sucesso'], $_SESSION['erro']);

// 2. Configurações de Paginação
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// 3. Lógica de Busca com Categorias
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$tipo_busca = isset($_GET['tipo_busca']) ? $_GET['tipo_busca'] : 'todos';

$condicoes = [];
$params = [];

if (!empty($busca)) {
    if ($tipo_busca === 'cadastro') {
        $condicoes[] = "numero_cadastro LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } elseif ($tipo_busca === 'nome') {
        $condicoes[] = "nome LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } elseif ($tipo_busca === 'rg') {
        $condicoes[] = "rg LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } elseif ($tipo_busca === 'email') {
        $condicoes[] = "email LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } else {
        // Opção "Todos"
        $condicoes[] = "(numero_cadastro LIKE :busca OR nome LIKE :busca OR rg LIKE :busca OR email LIKE :busca OR telefone LIKE :busca)";
        $params[':busca'] = '%' . $busca . '%';
    }
}

$sql_where = !empty($condicoes) ? " WHERE " . implode(" AND ", $condicoes) : "";

// 4. Consulta para contar o TOTAL
$sql_count = "SELECT COUNT(*) FROM leitores" . $sql_where;
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $itens_por_pagina);

// 5. Consulta Principal
$sql = "SELECT * FROM leitores" . $sql_where . " ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

foreach ($params as $chave => $valor) {
    $stmt->bindValue($chave, $valor);
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
                <select name="tipo_busca" class="form-select shadow-sm w-auto" onchange="this.form.submit()">
                    <option value="todos" <?= $tipo_busca == 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="cadastro" <?= $tipo_busca == 'cadastro' ? 'selected' : '' ?>>Nº Cadastro</option>
                    <option value="nome" <?= $tipo_busca == 'nome' ? 'selected' : '' ?>>Nome</option>
                    <option value="rg" <?= $tipo_busca == 'rg' ? 'selected' : '' ?>>RG</option>
                    <option value="email" <?= $tipo_busca == 'email' ? 'selected' : '' ?>>E-mail</option>
                </select>

                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="busca" id="inputBusca" class="form-control border-start-0" 
                           placeholder="Pesquisar leitores..." 
                           value="<?= htmlspecialchars($busca) ?>" autocomplete="off">
                </div>
                
                <?php if (!empty($busca)): ?>
                    <a href="listar_leitores.php" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?= $mensagem_sucesso ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($mensagem_erro): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $mensagem_erro ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    <?php if (count($leitores) === 0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash d-block mb-2 fa-2x opacity-25"></i>
                                Nenhum leitor encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leitores as $leitor): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <span class="badge bg-light text-dark border">#<?= $leitor['numero_cadastro'] ?></span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block"><?= htmlspecialchars($leitor['nome']) ?></span>
                                    <span class="text-muted small">F: <?= htmlspecialchars($leitor['filiacao']) ?></span>
                                </td>
                                <td>
                                    <div class="small"><i class="fa-solid fa-id-card me-1 opacity-50"></i> RG: <?= htmlspecialchars($leitor['rg']) ?></div>
                                    <div class="small text-truncate" style="max-width: 180px;"><i class="fa-solid fa-envelope me-1 opacity-50"></i> <?= htmlspecialchars($leitor['email']) ?></div>
                                </td>
                                <td class="small">
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($leitor['endereco']) ?>">
                                        <?= htmlspecialchars($leitor['endereco']) ?>
                                    </span>
                                    <div class="text-muted" style="font-size: 0.7rem;">Desde: <?= date('d/m/Y', strtotime($leitor['data_cadastro'])) ?></div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="editar_leitor.php?id=<?= $leitor['id'] ?>" class="btn btn-white btn-sm border text-primary"><i class="fa-solid fa-user-pen"></i></a>
                                        <form action="excluir_leitor.php" method="post" class="d-inline" onsubmit="return confirm('Excluir leitor permanentemente?');">
                                            <input type="hidden" name="id" value="<?= $leitor['id'] ?>">
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
                            <a class="page-link <?= $pagina_atual == $i ? 'bg-dark border-dark text-white' : 'text-dark' ?>" 
                               href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>&tipo_busca=<?= $tipo_busca ?>"><?= $i ?></a>
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
        const length = inputBusca.value.length;
        inputBusca.focus();
        inputBusca.setSelectionRange(length, length);
    }
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>