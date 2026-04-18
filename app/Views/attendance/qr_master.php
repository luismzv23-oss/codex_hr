<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Quiosco de Asistencia (Generador QR)<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 text-center mt-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white p-3">
                <h4 class="m-0"><i class="bi bi-qr-code-scan"></i> Terminal de Recepción HR</h4>
            </div>
            <div class="card-body p-5">
                <p class="text-muted mb-4">Muestra esta pantalla en la tablet de recepción de la empresa o imprímelo hoy diariamente. Los empleados deberán escanear el código para autorizar su ficha de entrada / salida.</p>
                
                <div id="qrcode" class="d-flex justify-content-center mb-4"></div>
                
                <div class="alert alert-info">
                    Token Criptográfico del Servidor (Solo Hoy): <br> <small class="text-monospace fw-bold"><?= esc($qrToken) ?></small>
                </div>
            </div>
            <div class="card-footer bg-light">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Volver al Dashboard</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Inyección de QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "<?= esc($qrToken) ?>",
        width: 300,
        height: 300,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>
<?= $this->endSection() ?>
