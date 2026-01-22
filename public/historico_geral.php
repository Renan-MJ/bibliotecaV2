<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

// 1. Configurações de Paginação
$itens_por_pagina = 15;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// 2. Consulta Unificada (UNION) - Puxando as datas apenas para cá
$sql = "
    (SELECT 'livro' as tipo, titulo as descricao, data_cadastro as data_ref, id FROM livros)
    UNION ALL
    (SELECT 'leitor' as tipo, nome as descricao, data_cadastro as data_ref, id FROM leitores)
    UNION ALL
    (SELECT 'emprestimo' as tipo, CONCAT('Registro de Empréstimo') as descricao, data_emprestimo as data_ref, id FROM emprestimos)
    ORDER BY data_ref DESC 
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contagem para paginação
$total = $pdo->query("SELECT (SELECT COUNT(*) FROM livros) + (SELECT COUNT(*) FROM leitores) + (SELECT COUNT(*) FROM emprestimos)")->fetchColumn();
$total_paginas = ceil($total / $itens_por_pagina);
?>

<div class="container py-5">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold text-dark mb-0">Histórico de Atividades</h2>
            <p class="text-muted">Acompanhe novos cadastros e movimentações por ordem cronológica.</p>
        </div>
        <i class="fa-solid fa-clock-rotate-left fa-3x text-light"></i>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4 py-3">Quando</th>
                        <th class="py-3">Ação / Evento</th>
                        <th class="py-3">Registro Relacionado</th>
                        <th class="py-3 text-end pe-4">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($atividades as $at): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="text-dark fw-medium"><?= date('d/m/Y', strtotime($at['data_ref'])) ?></div>
                                <div class="text-muted small"><?= date('H:i', strtotime($at['data_ref'])) ?>h</div>
                            </td>
                            <td>
                                <?php 
                                    switch($at['tipo']){
                                        case 'livro': 
                                            echo '<span class="badge bg-primary-subtle text-primary"><i class="fa-solid fa-book-medical me-1"></i> Livro Cadastrado</span>'; 
                                            break;
                                        case 'leitor': 
                                            echo '<span class="badge bg-info-subtle text-info-emphasis"><i class="fa-solid fa-user-check me-1"></i> Leitor Cadastrado</span>'; 
                                            break;
                                        case 'emprestimo': 
                                            echo '<span class="badge bg-success-subtle text-success"><i class="fa-solid fa-sync me-1"></i> Novo Empréstimo</span>'; 
                                            break;
                                    }
                                ?>
                            </td>
                            <td class="text-dark">
                                <?= htmlspecialchars($at['descricao']) ?>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">ID interno: #<?= $at['id'] ?></small>
                            </td>
                            <td class="text-end pe-4">
                                <?php 
                                    $link = ($at['tipo'] == 'livro') ? "editar_livro.php?id=" : (($at['tipo'] == 'leitor') ? "editar_leitor.php?id=" : "editar_emprestimo.php?id=");
                                ?>
                                <a href="<?= $link . $at['id'] ?>" class="btn btn-sm btn-light border text-primary">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_paginas > 1): ?>
        <div class="card-footer bg-white py-3">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= $pagina_atual == $i ? 'active' : '' ?>">
                            <a class="page-link <?= $pagina_atual == $i ? 'bg-dark border-dark text-white' : 'text-dark' ?>" href="?pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>