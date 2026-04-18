<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Confirmar Check-in QR<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-qr-code-scan"></i> Confirmación de Ingreso</span>
                <span class="badge bg-light text-dark"><?= date('d/m/Y H:i') ?></span>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="mb-1"><?= esc($qrPoint['name']) ?></h4>
                    <?php if (!empty($qrPoint['user_name'])): ?>
                        <p class="text-muted mb-1">Empleado: <?= esc($qrPoint['user_name']) ?></p>
                    <?php endif; ?>
                    <p class="text-muted mb-1"><?= esc($qrPoint['location'] ?? 'Sin ubicación registrada') ?></p>
                    <?php if (!empty($qrPoint['description'])): ?>
                        <p class="mb-0"><?= esc($qrPoint['description']) ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($activeShift): ?>
                    <div class="alert alert-warning">
                        Ya tienes una jornada abierta desde las <?= date('H:i', strtotime($activeShift['check_in'])) ?>.
                        Debes cerrarla antes de registrar un nuevo ingreso QR.
                    </div>
                    <form action="<?= base_url('attendance/checkout') ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-left"></i> Finalizar jornada actual
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        Vas a registrar tu ingreso usando el código QR <strong><?= esc($qrPoint['name']) ?></strong>.
                    </div>
                    <form action="<?= base_url('attendance/checkin-qr') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="qr_point_id" value="<?= $qrPoint['id'] ?>">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check2-circle"></i> Confirmar check-in
                            </button>
                            <a href="<?= base_url('attendance/scan') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Volver al lector
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
