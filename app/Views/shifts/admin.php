<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Gestión de Turnos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">Programar Turno</div>
            <div class="card-body">
                <form action="<?= base_url('shifts/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Empleado</label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Turno Libre (No Asignado) --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Fecha del turno</label>
                        <input type="date" name="shift_date" class="form-control" min="<?= esc($minAssignDate) ?>" required>
                        <small class="text-muted">Solo se permiten turnos con al menos 2 días de anticipación.</small>
                    </div>
                    <div class="mb-3">
                        <label>Horario base</label>
                        <select name="schedule_id" class="form-select">
                            <option value="">-- Asignación libre --</option>
                            <?php foreach ($schedules as $schedule): ?>
                                <option value="<?= $schedule['id'] ?>">
                                    <?= esc($schedule['name']) ?> (<?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label>Hora inicio libre</label>
                            <input type="time" name="manual_start_time" class="form-control">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label>Hora fin libre</label>
                            <input type="time" name="manual_end_time" class="form-control">
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Si eliges horario base, las horas manuales se ignoran. Si no, puedes crear un turno libre.</small>
                    <button class="btn btn-success w-100">Programar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header border-bottom">Listado de Programación</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Horario base</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shifts as $s): ?>
                            <tr class="<?= (int) $s['is_active'] !== 1 ? 'table-secondary' : '' ?>">
                                <td><?= esc($s['name'] ?? 'Turno libre') ?></td>
                                <td><?= esc($s['schedule_name'] ?? 'Asignación libre') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($s['start_time'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($s['end_time'])) ?></td>
                                <td>
                                    <?php if ((int) $s['is_active'] !== 1): ?>
                                        <span class="badge bg-secondary">Deshabilitado</span>
                                    <?php elseif (($s['status'] ?? 'pending') === 'approved'): ?>
                                        <span class="badge bg-success">Aprobado</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editShift<?= $s['id'] ?>">Editar</button>
                                        <form action="<?= base_url('shifts/toggle/' . $s['id']) ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-secondary"><?= (int) $s['is_active'] === 1 ? 'Deshabilitar' : 'Habilitar' ?></button>
                                        </form>
                                        <?php if ((int) $s['is_active'] === 1 && ($s['status'] ?? 'pending') !== 'approved'): ?>
                                        <form action="<?= base_url('shifts/approve/' . $s['id']) ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editShift<?= $s['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="<?= base_url('shifts/update/' . $s['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Editar Turno</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Empleado</label>
                                                    <select name="user_id" class="form-select">
                                                        <option value="">-- Turno Libre (No Asignado) --</option>
                                                        <?php foreach ($users as $u): ?>
                                                            <option value="<?= $u['id'] ?>" <?= (int) ($s['user_id'] ?? 0) === (int) $u['id'] ? 'selected' : '' ?>>
                                                                <?= esc($u['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Fecha del turno</label>
                                                    <input type="date" name="shift_date" class="form-control" min="<?= esc($minAssignDate) ?>" value="<?= date('Y-m-d', strtotime($s['start_time'])) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Horario base</label>
                                                    <select name="schedule_id" class="form-select">
                                                        <option value="">-- Asignación libre --</option>
                                                        <?php foreach ($schedules as $schedule): ?>
                                                            <option value="<?= $schedule['id'] ?>" <?= (int) ($s['schedule_id'] ?? 0) === (int) $schedule['id'] ? 'selected' : '' ?>>
                                                                <?= esc($schedule['name']) ?> (<?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 mb-3">
                                                        <label>Hora inicio libre</label>
                                                        <input type="time" name="manual_start_time" class="form-control" value="<?= date('H:i', strtotime($s['start_time'])) ?>">
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <label>Hora fin libre</label>
                                                        <input type="time" name="manual_end_time" class="form-control" value="<?= date('H:i', strtotime($s['end_time'])) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Guardar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($shifts)): ?>
                                <tr><td colspan="6" class="text-center text-muted">No hay turnos programados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
