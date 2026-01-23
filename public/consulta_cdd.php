<?php 
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php'; 

// Definir o caminho do ficheiro
// Se o seu consulta_cdd.php estiver na raiz, o caminho é 'uploads/tabela_cdd.pdf'
// Se estiver dentro de uma pasta (ex: admin/), use '../uploads/tabela_cdd.pdf'
$caminho_relativo = 'uploads/tabela_cdd.pdf';
$caminho_absoluto = __DIR__ . '/' . $caminho_relativo;

// Se o ficheiro não estiver na raiz, tenta um nível acima
if (!file_exists($caminho_absoluto)) {
    $caminho_relativo = '../uploads/tabela_cdd.pdf';
    $caminho_absoluto = __DIR__ . '/' . $caminho_relativo;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-0 shadow-sm p-5">
                <i class="fa-solid fa-file-pdf text-danger fa-4x mb-4"></i>
                <h2 class="fw-bold text-dark">Documentação CDD</h2>
                <p class="text-muted mb-4">Classificação Decimal de Dewey - Guia Completo para Bibliotecas</p>

                <?php if (file_exists($caminho_absoluto)): ?>
                    <div class="alert alert-success border-0 bg-success-subtle mb-4">
                        <i class="fa-solid fa-check-circle me-2"></i> Ficheiro pronto para download.
                    </div>
                    
                    <a href="<?= $caminho_relativo ?>" download="CDD_Completo.pdf" class="btn btn-danger btn-lg px-5 shadow">
                        <i class="fa-solid fa-cloud-arrow-down me-2"></i> DESCARREGAR PDF
                    </a>
                <?php else: ?>
                    <div class="alert alert-danger border-0">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> 
                        <strong>Erro de Localização:</strong><br>
                        O sistema não encontrou o ficheiro em:<br>
                        <small class="text-break"><code><?= $caminho_absoluto ?></code></small>
                    </div>
                    <p class="small text-muted mt-3">Verifique se o ficheiro está na pasta <b>uploads</b> e se o nome é <b>tabela_cdd.pdf</b></p>
                <?php endif; ?>
            </div>

            <div class="mt-5 text-start">
                <h5 class="fw-bold text-secondary text-center mb-4">Resumo de Referência Rápida</h5>
                <div class="row g-2">
                    <?php
                    $resumo = [
                        "000" => "Generalidades", "100" => "Filosofia/Psicologia",
                        "200" => "Religião", "300" => "Ciências Sociais",
                        "400" => "Linguística", "500" => "Ciências Puras",
                        "600" => "Tecnologia", "700" => "Artes/Lazer",
                        "800" => "Literatura", "900" => "História/Geografia"
                    ];
                    foreach ($resumo as $n => $t) echo "<div class='col-6 col-md-4'><div class='p-2 border rounded bg-white small'><b>$n</b> - $t</div></div>";
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>