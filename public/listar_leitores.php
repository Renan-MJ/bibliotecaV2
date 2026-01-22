<?php
require_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/includes/header.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$mensagem_sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['sucesso']);

$sql = "SELECT * FROM leitores ORDER BY id DESC";
$stmt = $pdo->query($sql);
$leitores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Gestão de Leitores</h2>
            <p class="text-muted small">Controle de cidadãos e usuários cadastrados na biblioteca municipal.</p>
        </div>
        <a href="cadastrar_leitor.php" class="btn btn-primary shadow-sm d-flex align-items-center gap-2" style="background-color: #0f172a; border: none;">
            <i class="fa-solid fa-user-plus"></i> Novo Leitor
        </a>
    </div>

    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div><?= $mensagem_sucesso ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3" style="width: 100px;">Nº Cadastro</th>
                        <th class="py-3">Cidadão / Filiação</th>
                        <th class="py-3">Documentação / Contato</th>
                        <th class="py-3">Endereço</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-secondary">
                    <?php if (count($leitores) === 0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">Nenhum leitor encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leitores as $leitor): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <span class="badge bg-slate-200 text-dark border">#<?= $leitor['numero_cadastro'] ?></span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block"><?= htmlspecialchars($leitor['nome']) ?></span>
                                    <span class="text-muted small">Pai/Mãe: <?= htmlspecialchars($leitor['filiacao']) ?></span>
                                </td>
                                <td>
                                    <div class="small"><i class="fa-solid fa-address-card me-1 opacity-50"></i> RG: <?= htmlspecialchars($leitor['rg']) ?></div>
                                    <div class="small"><i class="fa-solid fa-envelope me-1 opacity-50"></i> <?= htmlspecialchars($leitor['email']) ?></div>
                                    <div class="small"><i class="fa-solid fa-phone me-1 opacity-50"></i> <?= htmlspecialchars($leitor['telefone']) ?></div>
                                </td>
                                <td class="small">
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($leitor['endereco']) ?>">
                                        <i class="fa-solid fa-location-dot me-1 text-muted"></i> <?= htmlspecialchars($leitor['endereco']) ?>
                                    </span>
                                    <div class="text-muted" style="font-size: 0.7rem;">Cadastrado em: <?= date('d/m/Y', strtotime($leitor['data_cadastro'])) ?></div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="editar_leitor.php?id=<?= $leitor['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Editar">
                                            <i class="fa-solid fa-user-pen"></i>
                                        </a>
                                        <form action="excluir_leitor.php" method="post" class="d-inline" 
                                              onsubmit="return confirm('Deseja excluir permanentemente este leitor?');">
                                            <input type="hidden" name="id" value="<?= $leitor['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
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
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>