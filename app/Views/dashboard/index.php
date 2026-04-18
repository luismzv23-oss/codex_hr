<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tu Próximo Turno</div>
                        <?php if ($nextShift): ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= date('d/m/Y H:i', strtotime($nextShift['start_time'])) ?>
                            </div>
                            <small class="text-muted d-block mt-2"><?= esc($nextShift['schedule_name'] ?? 'Turno asignado') ?></small>
                        <?php else: ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Sin turnos próximos</div>
                        <?php endif; ?>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Registro Diario</div>
            <div class="card-body text-center p-5">
                <h4 id="active-timer" data-checkin="<?= esc($activeAttendance['check_in'] ?? '') ?>">
                    00:00:00
                </h4>
                <?php if ($activeAttendance): ?>
                    <p class="text-muted">Jornada iniciada a las <?= date('H:i', strtotime($activeAttendance['check_in'])) ?></p>
                    <form action="<?= base_url('attendance/checkout') ?>" method="POST">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger btn-lg px-5 py-3 rounded-pill"><i class="bi bi-box-arrow-left"></i> Finalizar Jornada</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">El cálculo inicia automáticamente cuando haces check-in.</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <form action="<?= base_url('attendance/checkin') ?>" method="POST">
                            <?= csrf_field() ?>
                            <button class="btn btn-success btn-lg px-5 py-3 rounded-pill"><i class="bi bi-box-arrow-in-right"></i> Iniciar Jornada</button>
                        </form>
                        <a href="<?= base_url('attendance/scan') ?>" class="btn btn-outline-primary btn-lg px-5 py-3 rounded-pill">
                            <i class="bi bi-qr-code-scan"></i> Check-in con QR
                        </a>
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
    const timerElement = document.getElementById('active-timer');
    const checkinValue = timerElement?.dataset.checkin;
    if (!timerElement || !checkinValue) {
        return;
    }

    const checkinDate = new Date(checkinValue.replace(' ', 'T'));
    const render = () => {
        const diffMs = Date.now() - checkinDate.getTime();
        const totalSeconds = Math.max(0, Math.floor(diffMs / 1000));
        const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
        const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');
        timerElement.textContent = `${hours}:${minutes}:${seconds}`;
    };

    render();
    setInterval(render, 1000);
});
</script>
<?= $this->endSection() ?>
