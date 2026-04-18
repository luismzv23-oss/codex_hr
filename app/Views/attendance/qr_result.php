<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Resultado Check-in QR<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center py-4">
    <div class="col-lg-7">
        <div class="card shadow border-0">
            <div class="card-header bg-<?= $result['success'] ? 'success' : 'danger' ?> text-white text-center py-4">
                <h3 class="mb-1">
                    <i class="bi bi-<?= $result['success'] ? 'check2-circle' : 'exclamation-triangle' ?>"></i>
                    <?= $result['success'] ? 'Check-in registrado' : 'No fue posible registrar el ingreso' ?>
                </h3>
                <small><?= esc($qrPoint['user_name'] ?? 'Empleado') ?></small>
            </div>
            <div class="card-body p-4 p-md-5 text-center">
                <p class="lead"><?= esc($result['message']) ?></p>
                <div class="text-muted mb-4">
                    <div>Departamento: <?= esc($qrPoint['department_name'] ?? 'Sin departamento') ?></div>
                    <div>Documento: <?= esc($qrPoint['document_id'] ?? 'Sin documento') ?></div>
                    <?php if (!empty($result['check_in'])): ?>
                        <div>Hora de registro: <?= date('d/m/Y H:i', strtotime($result['check_in'])) ?></div>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('auth/login?document_id=' . urlencode($qrPoint['document_id'] ?? '')) ?>" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-arrow-left"></i> Volver a mi QR
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
