<?php include_once __DIR__ . '/includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="listar_leitores.php" class="text-decoration-none">Leitores</a></li>
                    <li class="breadcrumb-item active">Novo Cadastro</li>
                </ol>
            </nav>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white p-4" style="background-color: #0f172a !important;">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-user-plus me-2 text-info"></i> Cadastrar Novo Leitor</h4>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="salvar_leitor.php" method="post">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-semibold text-muted">Nº de Cadastro</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="number" name="numero_cadastro" class="form-control" placeholder="0000" required>
                                </div>
                            </div>

                            <div class="col-md-8 mb-4">
                                <label class="form-label fw-semibold text-muted">Nome Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" name="nome" class="form-control" placeholder="Nome do cidadão" required>
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold text-muted">Data de Nascimento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-day"></i></span>
                                    <input type="date" name="data_nascimento" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold text-muted">Filiação</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-users-rectangle"></i></span>
                                    <input type="text" name="filiacao" class="form-control" placeholder="Nome dos pais">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-muted">RG</label>
                                <input type="text" name="rg" class="form-control" placeholder="00.000.000-0">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-muted">Telefone</label>
                                <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold text-muted">E-mail</label>
                                <input type="email" name="email" class="form-control" placeholder="exemplo@email.com">
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold text-muted">Endereço Residencial</label>
                                <input type="text" name="endereco" class="form-control" placeholder="Rua, número, bairro...">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="listar_leitores.php" class="btn btn-light border">Cancelar</a>
                            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                                <i class="fa-solid fa-check me-2"></i> Finalizar Cadastro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>