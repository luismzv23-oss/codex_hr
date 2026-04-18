<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Acceso y Consulta QR<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4 align-items-stretch py-3 py-md-5">
    <div class="col-lg-6">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-header bg-primary text-white text-center py-4">
                <h3 class="font-weight-light my-2"><i class="bi bi-buildings"></i> Codex HR</h3>
                <small>Gestión Operativa de Personal</small>
            </div>
            <div class="card-body p-4 p-md-5">
                <h5 class="mb-3">Iniciar sesión</h5>
                <form action="<?= base_url('auth/login') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" required>
                        <label for="inputEmail">Correo electrónico</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Password" required>
                        <label for="inputPassword">Contraseña</label>
                    </div>
                    <button class="btn btn-primary w-100 py-3" type="submit">Acceder al Sistema</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-header bg-dark text-white py-4">
                <h4 class="mb-1"><i class="bi bi-qr-code"></i> Mi QR de Asistencia</h4>
                <small>Consulta tu código sin iniciar sesión</small>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="<?= base_url('auth/login') ?>" class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Documento</label>
                        <input type="text" name="document_id" class="form-control form-control-lg" value="<?= esc($documentId ?? '') ?>" placeholder="Ingresa tu documento" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success w-100 py-3">
                            <i class="bi bi-search"></i> Consultar mi QR
                        </button>
                    </div>
                </form>

                <?php if (!empty($publicQrError)): ?>
                    <div class="alert alert-warning"><?= esc($publicQrError) ?></div>
                <?php endif; ?>

                <?php if (!empty($publicQr)): ?>
                    <div class="text-center">
                        <div class="qr-code-public border rounded bg-white p-3 d-inline-block mb-3" data-url="<?= esc(base_url('attendance/qr/' . $publicQr['token']), 'attr') ?>"></div>
                        <h4 class="mb-1"><?= esc($publicQr['user_name']) ?></h4>
                        <p class="text-muted mb-1"><?= esc($publicQr['department_name'] ?? 'Sin departamento') ?></p>
                        <p class="text-muted mb-3">Documento: <?= esc($publicQr['document_id'] ?? 'Sin documento') ?></p>
                        <div class="alert alert-info text-start">
                            Escanea este QR para procesar el inicio de la jornada sin iniciar sesión.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border">
                        Introduce tu documento para visualizar tu QR activo.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.qr-code-public').forEach(function (element) {
        new QRCode(element, {
            text: element.dataset.url,
            width: 220,
            height: 220,
        });
    });
});
</script>
<?= $this->endSection() ?>
