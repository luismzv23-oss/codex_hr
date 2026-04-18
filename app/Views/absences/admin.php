<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Workflow: Aprobación de Licencias<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header bg-white py-3 border-bottom-primary">
                <h6 class="m-0 font-weight-bold text-primary">Panel de Aprobación Documental</h6>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Tipo / Motivo</th>
                            <th>Período</th>
                            <th>Adjunto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absences as $a): ?>
                        <tr>
                            <td class="align-middle fw-bold"><?= esc($a['name']) ?></td>
                            <td class="align-middle">
                                <span class="badge bg-secondary"><?= esc($a['type']) ?></span><br>
                                <small class="text-muted"><?= esc($a['reason'] ?? 'Sin observaciones') ?></small>
                            </td>
                            <td class="align-middle"><?= date('d/m/Y', strtotime($a['start_date'])) ?> al <?= date('d/m/Y', strtotime($a['end_date'])) ?></td>
                            <td class="align-middle text-center">
                                <?php if ($a['attachment']): ?>
                                    <a href="<?= base_url('absences/attachment/' . $a['id']) ?>" target="_blank" class="btn btn-sm btn-info text-white">Abrir Documento</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <?php if ($a['status'] == 'Pendiente'): ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php elseif ($a['status'] == 'Aprobado'): ?>
                                    <span class="badge bg-success">Aprobado</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rechazado</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <?php if ($a['status'] == 'Pendiente'): ?>
                                <form action="<?= base_url('absences/update_status/' . $a['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="Aprobado">
                                    <button class="btn btn-sm btn-success" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <form action="<?= base_url('absences/update_status/' . $a['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="Rechazado">
                                    <button class="btn btn-sm btn-danger" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                                </form>
                                <?php else: ?>
                                    <i class="bi bi-lock text-muted" title="Ya procesado"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($absences)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No existen solicitudes de licencias registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
