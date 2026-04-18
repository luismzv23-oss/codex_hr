<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Horarios Base de la Empresa<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">Nuevo Horario Base</div>
            <div class="card-body">
                <form action="<?= base_url('schedules/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Nombre del Horario</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Hora de Inicio</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Hora de Fin</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Color</label>
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#3788d8" required>
                    </div>
                    <button class="btn btn-success w-100"><i class="bi bi-save"></i> Guardar Horario</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Plantillas de Horarios Activas</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Horario</th>
                                <th>Color</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td><?= esc($s['name']) ?></td>
                                <td><?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?></td>
                                <td><div style="width: 20px; height: 20px; background-color: <?= esc($s['color']) ?>; border-radius: 50%;"></div></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSched<?= $s['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <form action="<?= base_url('schedules/delete/' . $s['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Seguro de borrar este horario base?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editSched<?= $s['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="<?= base_url('schedules/update/' . $s['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title text-dark">Editar Horario Base</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Nombre del Horario</label>
                                                    <input type="text" name="name" value="<?= esc($s['name']) ?>" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Hora de Inicio</label>
                                                    <input type="time" name="start_time" value="<?= $s['start_time'] ?>" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Hora de Fin</label>
                                                    <input type="time" name="end_time" value="<?= $s['end_time'] ?>" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Color</label>
                                                    <input type="color" name="color" value="<?= $s['color'] ?>" class="form-control form-control-color w-100" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Actualizar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($schedules)): ?>
                                <tr><td colspan="4" class="text-center text-muted">Aún no se han creado horarios.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
