<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Puntos QR<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">Crear Punto QR</div>
    <div class="card-body">
        <form action="<?= base_url('qr-points/store') ?>" method="POST" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ubicación</label>
                <input type="text" name="location" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Descripción</label>
                <input type="text" name="description" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Crear punto
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <?php foreach ($points as $point): ?>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100 border-<?= (int) $point['is_active'] === 1 ? 'success' : 'secondary' ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= esc($point['name']) ?></strong>
                        <small class="d-block text-muted"><?= esc($point['location'] ?? 'Sin ubicación') ?></small>
                    </div>
                    <span class="badge bg-<?= (int) $point['is_active'] === 1 ? 'success' : 'secondary' ?>">
                        <?= (int) $point['is_active'] === 1 ? 'Activo' : 'Deshabilitado' ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="qr-code border rounded bg-white p-3 d-inline-block" data-url="<?= esc(base_url('attendance/qr/' . $point['token']), 'attr') ?>"></div>
                    </div>
                    <p class="small text-muted mb-3"><?= esc($point['description'] ?? 'Sin descripción') ?></p>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" class="form-control" readonly value="<?= base_url('attendance/qr/' . $point['token']) ?>">
                    </div>

                    <form action="<?= base_url('qr-points/update/' . $point['id']) ?>" method="POST" class="row g-2 mb-2">
                        <?= csrf_field() ?>
                        <div class="col-12">
                            <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($point['name']) ?>" required>
                        </div>
                        <div class="col-12">
                            <input type="text" name="location" class="form-control form-control-sm" value="<?= esc($point['location'] ?? '') ?>" placeholder="Ubicación">
                        </div>
                        <div class="col-12">
                            <input type="text" name="description" class="form-control form-control-sm" value="<?= esc($point['description'] ?? '') ?>" placeholder="Descripción">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-pencil-square"></i> Guardar cambios
                            </button>
                        </div>
                    </form>

                    <div class="d-grid gap-2">
                        <form action="<?= base_url('qr-points/toggle/' . $point['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-<?= (int) $point['is_active'] === 1 ? 'warning' : 'success' ?> btn-sm w-100">
                                <i class="bi bi-power"></i> <?= (int) $point['is_active'] === 1 ? 'Deshabilitar' : 'Habilitar' ?>
                            </button>
                        </form>
                        <form action="<?= base_url('qr-points/regenerate/' . $point['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-dark btn-sm w-100">
                                <i class="bi bi-arrow-repeat"></i> Regenerar token
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($points)): ?>
        <div class="col-12">
            <div class="alert alert-secondary">Aún no hay puntos QR registrados.</div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.qr-code').forEach(function (element) {
        new QRCode(element, {
            text: element.dataset.url,
            width: 180,
            height: 180,
        });
    });
});
</script>
<?= $this->endSection() ?>
