<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Códigos QR del Personal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">Generar QR por Empleado</div>
    <div class="card-body">
        <form action="<?= base_url('qr-points/store') ?>" method="POST" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-5">
                <label class="form-label">Empleado</label>
                <select name="user_id" class="form-select" required>
                    <option value="">Seleccione personal...</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee['id'] ?>" <?= old('user_id') == $employee['id'] ? 'selected' : '' ?>>
                            <?= esc($employee['name']) ?> - <?= esc($employee['email']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ubicación sugerida</label>
                <input type="text" name="location" class="form-control" value="<?= esc(old('location', '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Descripción</label>
                <input type="text" name="description" class="form-control" value="<?= esc(old('description', '')) ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-person-badge"></i> Generar código QR
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-white">
        <form method="GET" action="<?= base_url('qr-points') ?>" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" value="<?= esc($search) ?>" placeholder="Nombre, correo, documento o etiqueta QR">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="<?= base_url('qr-points') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                </a>
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
                        <strong><?= esc($point['user_name'] ?? $point['name']) ?></strong>
                        <small class="d-block text-muted"><?= esc($point['user_email'] ?? 'Sin correo') ?></small>
                        <?php if (!empty($point['document_id'])): ?>
                            <small class="d-block text-muted">Doc: <?= esc($point['document_id']) ?></small>
                        <?php endif; ?>
                    </div>
                    <span class="badge bg-<?= (int) $point['is_active'] === 1 ? 'success' : 'secondary' ?>">
                        <?= (int) $point['is_active'] === 1 ? 'Activo' : 'Deshabilitado' ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="qr-code border rounded bg-white p-3 d-inline-block" data-url="<?= esc(base_url('attendance/qr/' . $point['token']), 'attr') ?>"></div>
                    </div>
                    <p class="small text-muted mb-1"><strong>Etiqueta:</strong> <?= esc($point['name']) ?></p>
                    <p class="small text-muted mb-3">
                        <strong>Ubicación:</strong> <?= esc($point['location'] ?? 'Sin ubicación') ?><br>
                        <strong>Descripción:</strong> <?= esc($point['description'] ?? 'Sin descripción') ?>
                    </p>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" class="form-control" readonly value="<?= base_url('attendance/qr/' . $point['token']) ?>">
                    </div>

                    <form action="<?= base_url('qr-points/update/' . $point['id']) ?>" method="POST" class="row g-2 mb-2">
                        <?= csrf_field() ?>
                        <div class="col-12">
                            <select name="user_id" class="form-select form-select-sm" required>
                                <option value="">Seleccione personal...</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee['id'] ?>" <?= (int) $point['user_id'] === (int) $employee['id'] ? 'selected' : '' ?>>
                                        <?= esc($employee['name']) ?> - <?= esc($employee['email']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
            <div class="alert alert-secondary">No se encontraron códigos QR para la búsqueda indicada.</div>
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
