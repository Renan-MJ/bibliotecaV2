<?php 
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php'; 

// --- Coleta de Dados para o Dashboard ---

// 1. Total de Livros e Disponibilidade
$totalLivros = $pdo->query("SELECT count(*) FROM livros")->fetchColumn();
$livrosDisponiveis = $pdo->query("SELECT count(*) FROM livros WHERE status = 'Disponível'")->fetchColumn();
$percentualDisponivel = ($totalLivros > 0) ? round(($livrosDisponiveis / $totalLivros) * 100) : 0;

// 2. Total de Leitores
$totalLeitores = $pdo->query("SELECT count(*) FROM leitores")->fetchColumn();

// 3. Empréstimos Ativos e Atrasados
$dataHoje = date('Y-m-d');
$emprestimosAtivos = $pdo->query("SELECT count(*) FROM emprestimos WHERE data_devolucao_real IS NULL")->fetchColumn();
$atrasados = $pdo->query("SELECT count(*) FROM emprestimos WHERE data_devolucao_real IS NULL AND data_devolucao_prevista < '$dataHoje'")->fetchColumn();

// 4. Últimas Atividades
$ultimosEmprestimos = $pdo->query("
    SELECT l.titulo, le.nome, e.data_emprestimo 
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN leitores le ON e.leitor_id = le.id
    ORDER BY e.id DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Estilos para solidez visual e animação */
    .dashboard-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .icon-shape {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
</style>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark mb-1">Painel de Gestão Bibliotecária</h2>
            <p class="text-muted">Prefeitura Municipal - Bem-vindo ao centro de comando do acervo.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card border-0 border-top border-primary border-4 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 align-items-start">
                        <div class="icon-shape bg-primary bg-opacity-10">
                            <i class="fa-solid fa-book text-primary fs-4"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 border border-success-subtle">
                            <?= $percentualDisponivel ?>% Livre
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $totalLivros ?></h3>
                    <p class="text-secondary small fw-bold text-uppercase mb-0">Total do Acervo</p>
                    <div class="progress mt-3" style="height: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar bg-primary" style="width: <?= $percentualDisponivel ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card border-0 border-top border-info border-4 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 align-items-start">
                        <div class="icon-shape bg-info bg-opacity-10">
                            <i class="fa-solid fa-users text-info fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $totalLeitores ?></h3>
                    <p class="text-secondary small fw-bold text-uppercase mb-0">Cidadãos Cadastrados</p>
                    <div class="mt-3">
                        <a href="listar_leitores.php" class="text-info small text-decoration-none fw-bold">Gerenciar <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card border-0 border-top border-warning border-4 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 align-items-start">
                        <div class="icon-shape bg-warning bg-opacity-10">
                            <i class="fa-solid fa-handshake text-warning fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $emprestimosAtivos ?></h3>
                    <p class="text-secondary small fw-bold text-uppercase mb-0">Empréstimos Ativos</p>
                    <p class="mt-3 mb-0 small text-warning-emphasis fw-medium">Livros fora da estante</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card border-0 border-top border-danger border-4 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 align-items-start">
                        <div class="icon-shape bg-danger bg-opacity-10">
                            <i class="fa-solid fa-clock text-danger fs-4"></i>
                        </div>
                        <?php if($atrasados > 0): ?>
                            <span class="badge bg-danger animate-pulse">ALERTA</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $atrasados ?></h3>
                    <p class="text-secondary small fw-bold text-uppercase mb-0">Pendências</p>
                    <p class="mt-3 mb-0 small fw-bold <?= $atrasados > 0 ? 'text-danger' : 'text-muted' ?>">
                        <?= $atrasados > 0 ? 'Requer atenção imediata' : 'Nenhum atraso' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px;">
                <h5 class="fw-bold text-dark mb-4">Ações Rápidas</h5>
                <div class="d-grid gap-3">
                    <a href="cadastrar_emprestimo.php" class="btn btn-primary py-3 fw-bold rounded-3 shadow-sm border-0" style="background: #0f172a;">
                        <i class="fa-solid fa-plus-circle me-2"></i> Novo Empréstimo
                    </a>
                    <a href="cadastrar_livro.php" class="btn btn-light py-2 fw-semibold text-dark border">
                        <i class="fa-solid fa-book-medical me-2 text-primary"></i> Adicionar Livro
                    </a>
                    <a href="cadastrar_leitor.php" class="btn btn-light py-2 fw-semibold text-dark border">
                        <i class="fa-solid fa-user-plus me-2 text-info"></i> Adicionar Leitor
                    </a>
                    <hr class="my-3 opacity-10">
                    <a href="listar_emprestimos.php" class="btn btn-outline-secondary py-2 small fw-bold">
                        <i class="fa-solid fa-clipboard-list me-2"></i> Relatório de Fluxo
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0 text-dark">Últimas Movimentações</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Obra</th>
                                    <th>Cidadão</th>
                                    <th class="text-end pe-4">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ultimosEmprestimos as $ativ): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        <i class="fa-solid fa-book-bookmark me-2 text-primary-emphasis opacity-50"></i>
                                        <?= htmlspecialchars($ativ['titulo']) ?>
                                    </td>
                                    <td class="text-secondary"><?= htmlspecialchars($ativ['nome']) ?></td>
                                    <td class="text-end pe-4">
                                        <span class="badge bg-light text-dark fw-normal border"><?= date('d/m/Y', strtotime($ativ['data_emprestimo'])) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($ultimosEmprestimos)): ?>
                                    <tr><td colspan="3" class="text-center py-5 text-muted italic">Sem movimentações recentes no acervo.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 text-center">
                    <a href="historico_geral.php" class="text-primary text-decoration-none small fw-bold">Ver histórico completo <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>