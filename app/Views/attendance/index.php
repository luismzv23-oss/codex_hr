<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Control de Asistencia<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">Registro Diario</div>
            <div class="card-body text-center p-4">
                <i class="bi bi-clock-history text-primary d-block mb-3" style="font-size: 3rem;"></i>

                <?php if (!$activeShift): ?>
                <form action="<?= base_url('attendance/checkin') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button class="btn btn-success btn-lg w-100 shadow"><i class="bi bi-box-arrow-in-right"></i> Iniciar Jornada</button>
                    <small class="text-muted d-block mt-2">Registra tu hora de ingreso manual</small>
                </form>
                <?php if (session()->get('role') !== 'admin'): ?>
                    <a href="<?= base_url('attendance/scan') ?>" class="btn btn-outline-primary btn-lg w-100 shadow mt-3">
                        <i class="bi bi-qr-code-scan"></i> Ingresar con QR
                    </a>
                    <?php if (!empty($activeQrPoints)): ?>
                        <small class="text-muted d-block mt-2">También puedes escanear un QR desde tu teléfono o elegir un punto activo.</small>
                    <?php endif; ?>
                <?php endif; ?>
                <?php else: ?>
                <form action="<?= base_url('attendance/checkout') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger btn-lg w-100 shadow"><i class="bi bi-box-arrow-left"></i> Finalizar Jornada</button>
                    <small class="text-muted d-block mt-2">Salida pendiente desde <?= date('H:i', strtotime($activeShift['check_in'])) ?></small>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Historial de Fichajes</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <?php if (session()->get('role') == 'admin'): ?>
                                <th>Empleado</th>
                            <?php endif; ?>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Turno programado</th>
                            <th>Método</th>
                            <th>Duración Calculada</th>
                            <th>Tardanza</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                        <tr>
                            <?php if (session()->get('role') == 'admin'): ?>
                                <td class="fw-bold"><?= esc($r['name']) ?></td>
                            <?php endif; ?>
                            <td class="text-success fw-bold"><i class="bi bi-box-arrow-in-right"></i> <?= date('d/m/Y H:i', strtotime($r['check_in'])) ?></td>
                            <td>
                                <?php if ($r['check_out']): ?>
                                    <span class="text-danger"><i class="bi bi-box-arrow-left"></i> <?= date('d/m/Y H:i', strtotime($r['check_out'])) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-broadcast"></i> Turno Abierto</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($r['shift_start_time'])): ?>
                                    <?= date('d/m/Y H:i', strtotime($r['shift_start_time'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin turno asociado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($r['checkin_method'] ?? 'manual') === 'qr'): ?>
                                    <span class="badge bg-primary"><i class="bi bi-qr-code-scan"></i> <?= esc($r['qr_point_name'] ?? 'QR') ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-hand-index-thumb"></i> Manual</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $start = strtotime($r['check_in']);
                                $end = $r['check_out'] ? strtotime($r['check_out']) : time();
                                $durationSeconds = max(0, $end - $start);
                                $hours = floor($durationSeconds / 3600);
                                $minutes = floor(($durationSeconds % 3600) / 60);
                                echo $hours . ' hrs ' . $minutes . ' mins';
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($r['late_minutes']) && $r['late_minutes'] > 0): ?>
                                    <span class="badge bg-<?= $r['late_minutes'] > 15 ? 'danger' : 'warning text-dark' ?>">
                                        <?= (int) $r['late_minutes'] ?> min
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Dentro de horario</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="7" class="text-center text-muted">Aún no hay registros de asistencia.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
