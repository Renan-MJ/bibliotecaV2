<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

// Pega apenas livros que estão 'Disponível'
$livros = $pdo->query("SELECT id, titulo, autor FROM livros WHERE status = 'Disponível' ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);

// Pega todos os leitores
$leitores = $pdo->query("SELECT id, nome, numero_cadastro FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Ajuste para o Select2 não quebrar dentro do input-group do Bootstrap */
    .select2-container--bootstrap-5 .select2-selection {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-color: #dee2e6 !important;
    }
    .select2-container {
        flex: 1 1 auto; /* Faz o select ocupar o espaço restante do input-group */
        width: 1% !important;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white p-4" style="background-color: #0f172a !important;">
                    <h4 class="mb-0 fw-bold">
                        <i class="fa-solid fa-handshake me-2 text-info"></i> Novo Registro de Empréstimo
                    </h4>
                    <small class="text-slate-400">Vincule um exemplar a um cidadão cadastrado.</small>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="salvar_emprestimo.php" method="post">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Livro / Obra</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-book"></i></span>
                                <select name="livro_id" id="livro_id" class="form-select select-busca" required>
                                    <option value="">Pesquisar por título ou autor...</option>
                                    <?php foreach ($livros as $livro): ?>
                                        <option value="<?= $livro['id'] ?>">
                                            <?= htmlspecialchars($livro['titulo']) ?> (ID: <?= $livro['id'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if(empty($livros)): ?>
                                <small class="text-danger">Atenção: Não há livros disponíveis no momento.</small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Leitor / Cidadão</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-user"></i></span>
                                <select name="leitor_id" id="leitor_id" class="form-select select-busca" required>
                                    <option value="">Pesquisar por nome ou nº cadastro...</option>
                                    <?php foreach ($leitores as $leitor): ?>
                                        <option value="<?= $leitor['id'] ?>">
                                            <?= htmlspecialchars($leitor['nome']) ?> (Nº: <?= $leitor['numero_cadastro'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Data de Saída</label>
                                <input type="date" name="data_emprestimo" id="data_emprestimo" 
                                       class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Previsão de Entrega</label>
                                <input type="date" name="data_devolucao_prevista" id="data_devolucao_prevista" 
                                       class="form-control border-info" required>
                            </div>
                        </div>

                        <div class="mb-4 col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase">Horário de Saída</label>
                            <input type="text" class="form-control bg-light" value="<?= date('H:i') ?>" readonly disabled>
                            <small class="text-muted" style="font-size: 0.7rem;">O horário é registrado automaticamente pelo sistema.</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <a href="listar_emprestimos.php" class="btn btn-light border">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold" style="background-color: #0f172a; border: none;">
                                Confirmar Retirada <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ativação do Select2 nos campos de busca
    $(document).ready(function() {
        $('.select-busca').select2({
            theme: 'bootstrap-5',
            placeholder: 'Clique para pesquisar...',
            allowClear: true,
            language: {
                noResults: function() { return "Nenhum registro encontrado"; }
            }
        });
    });

    const inputRetorno = document.getElementById('data_devolucao_prevista');

    function calcularSugestao() {
        let data = new Date(); 
        data.setDate(data.getDate() + 7); 
        inputRetorno.value = data.toISOString().split('T')[0];
    }

    window.onload = calcularSugestao;
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>