<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Mis Turnos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow">
    <div class="card-header border-bottom bg-primary text-white">Mi Calendario Semanal</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Horario base</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shifts as $s): ?>
                <tr>
                    <td><?= esc($s['schedule_name'] ?? 'Turno libre') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($s['start_time'])) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($s['end_time'])) ?></td>
                    <td>
                        <?php if (($s['status'] ?? 'pending') === 'approved'): ?>
                            <span class="badge bg-success">Aprobado</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($shifts)): ?>
                <tr>
                    <td colspan="4" class="text-center">No tienes turnos programados.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
