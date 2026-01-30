<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';

$mes_selecionado = $_GET['mes'] ?? date('m');
$ano_selecionado = $_GET['ano'] ?? date('Y');

$meses_extenso = [
    '01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril','05'=>'Maio','06'=>'Junho',
    '07'=>'Julho','08'=>'Agosto','09'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro'
];

try {
    // 1. Estatísticas Consolidadas
    $sql_stats = "SELECT 
                COUNT(*) as total_emprestados,
                COUNT(data_devolucao_real) as total_devolvidos,
                COUNT(DISTINCT leitor_id) as total_leitores
            FROM emprestimos 
            WHERE MONTH(data_emprestimo) = :mes AND YEAR(data_emprestimo) = :ano";

    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute([':mes' => $mes_selecionado, ':ano' => $ano_selecionado]);
    $dados = $stmt_stats->fetch(PDO::FETCH_ASSOC) ?: ['total_emprestados' => 0, 'total_devolvidos' => 0, 'total_leitores' => 0];

    // 2. Movimentação Detalhada
    $sql_lista = "SELECT e.*, l.nome as leitor_nome, b.titulo as livro_titulo 
                  FROM emprestimos e
                  JOIN leitores l ON e.leitor_id = l.id
                  JOIN livros b ON e.livro_id = b.id
                  WHERE MONTH(e.data_emprestimo) = :mes AND YEAR(e.data_emprestimo) = :ano
                  ORDER BY e.data_emprestimo ASC";
    
    $stmt_lista = $pdo->prepare($sql_lista);
    $stmt_lista->execute([':mes' => $mes_selecionado, ':ano' => $ano_selecionado]);
    $lista_detalhada = $stmt_lista->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao gerar relatório: " . $e->getMessage());
}

$total_pendentes = $dados['total_emprestados'] - $dados['total_devolvidos'];

include_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Estilos de Interface (Tela) */
    .btn-export { background-color: #dc3545; color: white; border: none; }
    
    /* ESTILOS EXCLUSIVOS PARA O PDF (IMPRESSÃO) */
    @media print {
        @page { size: portrait; margin: 2cm; } /* Aumentei um pouco a margem para a logo */
        header, footer, nav, .no-print, .btn-reload { display: none !important; }
        body { background-color: white !important; font-family: "Times New Roman", Times, serif !important; font-size: 11pt; }
        .container { width: 100% !important; max-width: 100% !important; margin: 0 !important; }
        
        /* CABEÇALHO ADMINISTRATIVO COM LOGO */
        .print-only-header { 
            display: flex !important; /* Usar flexbox para alinhar logo e texto */
            align-items: center; /* Centralizar verticalmente */
            justify-content: space-between; /* Espaço entre logo e texto */
            margin-bottom: 20px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px; 
        }
        .print-only-header img { 
            max-height: 80px; /* Tamanho da logo */
            width: auto;
            margin-right: 20px; /* Espaço entre a logo e o texto */
        }
        .print-only-header div {
            text-align: right; /* Alinhar texto à direita */
            flex-grow: 1; /* Permite que o texto ocupe o espaço restante */
        }
        .print-only-header h4, .print-only-header h5, .print-only-header h6, .print-only-header p {
            margin-bottom: 2px;
        }
        .print-only-header h4 { font-size: 14pt; }
        .print-only-header h5 { font-size: 12pt; }
        .print-only-header h6 { font-size: 11pt; }
        
        /* Tabelas Administrativas */
        .table { border: 1px solid #000 !important; width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .table th { background-color: #f2f2f2 !important; color: #000 !important; border: 1px solid #000 !important; text-transform: uppercase; font-size: 9pt; padding: 8px; }
        .table td { border: 1px solid #000 !important; padding: 6px !important; font-size: 10pt; }
        
        /* Cards transformados em lista de resumo */
        .row-stats { display: flex; justify-content: space-between; margin-bottom: 30px; border: 1px solid #000; padding: 15px; page-break-inside: avoid; }
        .stat-item { text-align: center; flex: 1; padding: 0 10px; }
        .stat-item:not(:last-child) { border-right: 1px solid #ddd; } /* Linhas divisórias suaves */
        .stat-item .h4 { margin-bottom: 5px; }
        
        /* Rodapé de Assinatura */
        .signature-section { display: block !important; margin-top: 50px; text-align: center; page-break-before: auto; page-break-inside: avoid; }
        .sig-line { border-top: 1px solid #000; width: 300px; margin: 50px auto 5px auto; }
        .sig-text { font-size: 10pt; line-height: 1.4; }
    }

    /* Esconde elementos específicos da impressão na visualização normal */
    .print-only-header, .signature-section { display: none; }
    .row-stats .stat-item:not(:last-child) { border-right: none; } /* Remover borda na tela */

    /* Estilos para os cards na tela, se desejar diferenciá-los visualmente */
    .row-stats-display .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
        background-color: #fff;
    }
    .row-stats-display .card .h4 { font-size: 1.5rem; }
    .row-stats-display .card .small { font-size: 0.75rem; color: #6c757d; }
</style>

<div class="container py-4">
    
    <div class="print-only-header">
        <img src="img/logo_prefeitura.png" alt="Logo da Prefeitura de [NOME DA CIDADE]"> <div>
            <h4 class="mb-0 fw-bold">Paraná</h4>
            <h5 class="mb-1">Prefeitura Municípal de Pontal do Paraná</h5>
            <h6 class="mb-3 text-uppercase">Relatório de Movimentação de Biblioteca</h6>
            <p class="small mb-0">Período: <?= $meses_extenso[$mes_selecionado] ?> / <?= $ano_selecionado ?></p>
            <p class="small mb-0">Emitido em: <?= date('d/m/Y H:i', strtotime('-3 hours')) ?></p>
        </div>
    </div>

    <div class="no-print d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
        <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-file-invoice me-2"></i>Relatório Administrativo</h4>
        <div class="d-flex gap-2">
            <form action="" method="GET" class="d-flex gap-2">
                <select name="mes" class="form-select w-auto">
                    <?php foreach ($meses_extenso as $num => $nome): ?>
                        <option value="<?= $num ?>" <?= $num == $mes_selecionado ? 'selected' : '' ?>><?= $nome ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="ano" class="form-control" value="<?= $ano_selecionado ?>" style="width: 100px;">
                <button type="submit" class="btn btn-primary bg-dark border-0">Filtrar</button>
            </form>
            <button onclick="window.print()" class="btn btn-danger"><i class="fa-solid fa-print me-2"></i>Gerar PDF para 1Doc</button>
        </div>
    </div>

    <div class="row row-stats-display g-4 mb-4 no-print">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white text-center">
                <h3 class="fw-bold text-primary mb-0"><?= $dados['total_emprestados'] ?></h3>
                <small class="text-muted fw-bold">TOTAL EMPRESTADOS</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white text-center">
                <h3 class="fw-bold text-success mb-0"><?= $dados['total_devolvidos'] ?></h3>
                <small class="text-muted fw-bold">TOTAL DEVOLVIDOS</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white text-center">
                <h3 class="fw-bold text-info mb-0"><?= $dados['total_leitores'] ?></h3>
                <small class="text-muted fw-bold">LEITORES ATIVOS</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white text-center">
                <h3 class="fw-bold text-warning mb-0"><?= $total_pendentes ?></h3>
                <small class="text-muted fw-bold">PENDENTES (EM ABERTO)</small>
            </div>
        </div>
    </div>

    <div class="row-stats mb-4">
        <div class="stat-item">
            <div class="small text-muted text-uppercase">Empréstimos Realizados</div>
            <div class="h4 fw-bold mb-0"><?= $dados['total_emprestados'] ?></div>
        </div>
        <div class="stat-item">
            <div class="small text-muted text-uppercase">Devoluções Confirmadas</div>
            <div class="h4 fw-bold mb-0 text-success"><?= $dados['total_devolvidos'] ?></div>
        </div>
        <div class="stat-item">
            <div class="small text-muted text-uppercase">Leitores Atendidos</div>
            <div class="h4 fw-bold mb-0"><?= $dados['total_leitores'] ?></div>
        </div>
        <div class="stat-item">
            <div class="small text-muted text-uppercase">Pendências Acumuladas</div>
            <div class="h4 fw-bold mb-0 text-danger"><?= $total_pendentes ?></div>
        </div>
    </div>


    <div class="card border-0 shadow-sm rounded-0">
        <div class="card-header bg-dark text-white no-print py-3">
            <h6 class="mb-0">Registros de Movimentação - <?= $meses_extenso[$mes_selecionado] ?> / <?= $ano_selecionado ?></h6>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
                <thead class="text-center">
                    <tr>
                        <th>DATA SAÍDA</th>
                        <th>NOME DO LEITOR</th>
                        <th>TÍTULO DO LIVRO</th>
                        <th>DATA DEVOLUÇÃO REAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lista_detalhada)): ?>
                        <tr><td colspan="4" class="text-center py-4">Nenhum registro de movimentação no período.</td></tr>
                    <?php else: ?>
                        <?php foreach ($lista_detalhada as $item): ?>
                            <tr>
                                <td class="text-center"><?= date('d/m/Y', strtotime($item['data_emprestimo'])) ?></td>
                                <td class="ps-2"><?= htmlspecialchars($item['leitor_nome']) ?></td>
                                <td class="ps-2 small"><?= htmlspecialchars($item['livro_titulo']) ?></td>
                                <td class="text-center">
                                    <?= (!empty($item['data_devolucao_real']) && $item['data_devolucao_real'] !== '0000-00-00') 
                                        ? date('d/m/Y', strtotime($item['data_devolucao_real'])) 
                                        : '<em>Em Aberto</em>' ?>
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