<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Escanear QR<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-qr-code-scan"></i> Lector de Check-in QR</span>
                <span class="badge bg-light text-dark">Móvil recomendado</span>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Puedes escanear el QR desde esta pantalla o usar la cámara del teléfono. Si el código pertenece a un punto válido, te llevaremos a la confirmación de ingreso.
                </p>

                <div class="ratio ratio-16x9 rounded overflow-hidden bg-dark mb-3">
                    <video id="qr-video" class="w-100 h-100 object-fit-cover" autoplay playsinline muted></video>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" id="start-scan" class="btn btn-primary">
                        <i class="bi bi-camera-video"></i> Activar cámara
                    </button>
                    <button type="button" id="stop-scan" class="btn btn-outline-secondary" disabled>
                        <i class="bi bi-stop-circle"></i> Detener
                    </button>
                </div>

                <div class="alert alert-info mb-3">
                    <strong>Estado:</strong> <span id="scan-status">Listo para iniciar el escaneo.</span>
                </div>

                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <label for="manual-token" class="form-label">Ingreso manual del enlace o token QR</label>
                        <div class="input-group">
                            <input type="text" id="manual-token" class="form-control" placeholder="Pega aquí la URL o el token del QR">
                            <button type="button" id="go-manual" class="btn btn-success">
                                <i class="bi bi-arrow-right-circle"></i> Abrir
                            </button>
                        </div>
                    </div>
                </div>

                <?php if (!empty($activeQrPoints)): ?>
                    <div class="mt-4">
                        <h6 class="text-primary mb-3">Puntos QR activos</h6>
                        <div class="list-group">
                            <?php foreach ($activeQrPoints as $point): ?>
                                <a href="<?= base_url('attendance/qr/' . $point['token']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong><?= esc($point['name']) ?></strong>
                                        <small class="d-block text-muted"><?= esc($point['location'] ?? 'Sin ubicación') ?></small>
                                    </span>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startBtn = document.getElementById('start-scan');
    const stopBtn = document.getElementById('stop-scan');
    const statusLabel = document.getElementById('scan-status');
    const video = document.getElementById('qr-video');
    const manualInput = document.getElementById('manual-token');
    const manualBtn = document.getElementById('go-manual');
    let stream = null;
    let detector = null;
    let rafId = null;

    const openToken = (value) => {
        if (!value) {
            statusLabel.textContent = 'Ingresa una URL o token válido.';
            return;
        }

        const trimmed = value.trim();
        const prefix = '<?= base_url('attendance/qr/') ?>';
        const destination = trimmed.startsWith('http')
            ? trimmed
            : prefix + trimmed.replace(prefix, '').replace(/^\/+/, '');
        window.location.href = destination;
    };

    const stopScan = () => {
        if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }
        video.srcObject = null;
        stopBtn.disabled = true;
        startBtn.disabled = false;
    };

    const tick = async () => {
        if (!detector || !video || video.readyState !== 4) {
            rafId = requestAnimationFrame(tick);
            return;
        }

        try {
            const codes = await detector.detect(video);
            if (codes.length > 0 && codes[0].rawValue) {
                stopScan();
                openToken(codes[0].rawValue);
                return;
            }
        } catch (error) {
            statusLabel.textContent = 'No se pudo leer el QR. Intenta nuevamente.';
        }

        rafId = requestAnimationFrame(tick);
    };

    startBtn?.addEventListener('click', async () => {
        if (!('BarcodeDetector' in window)) {
            statusLabel.textContent = 'Tu navegador no soporta lectura QR directa. Usa la cámara del teléfono o pega el enlace.';
            return;
        }

        try {
            detector = new BarcodeDetector({ formats: ['qr_code'] });
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            await video.play();
            statusLabel.textContent = 'Escaneando QR...';
            startBtn.disabled = true;
            stopBtn.disabled = false;
            rafId = requestAnimationFrame(tick);
        } catch (error) {
            statusLabel.textContent = 'No fue posible acceder a la cámara. Revisa permisos o usa la entrada manual.';
        }
    });

    stopBtn?.addEventListener('click', () => {
        stopScan();
        statusLabel.textContent = 'Escaneo detenido.';
    });

    manualBtn?.addEventListener('click', () => openToken(manualInput.value));
    manualInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            openToken(manualInput.value);
        }
    });
});
</script>
<?= $this->endSection() ?>
