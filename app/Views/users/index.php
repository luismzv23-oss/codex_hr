<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Gestión de Personal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">Registrar Empleado</div>
            <div class="card-body">
                <form action="<?= base_url('users/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Nombre Completo</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Documento</label>
                        <input type="text" name="document_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Rol de Sistema</label>
                        <select name="role" class="form-select" required>
                            <option value="employee">Empleado/Operario</option>
                            <option value="admin">Administrador Central</option>
                        </select>
                    </div>

                    <hr>
                    <h6 class="text-primary font-weight-bold mb-3">Legajo Laboral</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Departamento</label>
                            <select name="department_id" class="form-select">
                                <option value="">Ninguno</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= esc($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Contrato</label>
                            <select name="employee_type" class="form-select">
                                <option value="Permanente">Permanente</option>
                                <option value="Temporal">Temporal</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Salario</label>
                            <input type="number" step="0.01" name="salary_base" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Fecha Incorporación</label>
                            <input type="date" name="hire_date" class="form-control" required>
                        </div>
                    </div>

                    <button class="btn btn-success w-100"><i class="bi bi-person-plus"></i> Registrar Empleado</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Nómina de Personal Activo</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive overflow-visible">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= esc($u['name']) ?></div>
                                    <small class="text-muted">Doc: <?= esc($u['document_id'] ?? 'S/D') ?></small><br>
                                    <span class="badge bg-info text-dark mt-1"><?= esc($u['department_name'] ?? 'Sin Dpto') ?> (<?= esc($u['employee_type'] ?? 'Generico') ?>)</span>
                                    <span class="badge border border-secondary text-secondary mt-1 ms-1"><?= strtoupper($u['role']) ?></span>
                                </td>
                                <td class="align-middle"><?= esc($u['email']) ?></td>
                                <td class="align-middle">
                                    <span class="badge bg-<?= $u['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $u['is_active'] ? 'Activo' : 'Suspendido' ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group">
                                        <form action="<?= base_url('users/toggle_status/' . $u['id']) ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-<?= $u['is_active'] ? 'secondary' : 'success' ?>" title="<?= $u['is_active'] ? 'Suspender' : 'Reactivar' ?>">
                                                <i class="bi bi-power"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUser<?= $u['id'] ?>"><i class="bi bi-pencil"></i></button>
                                        <form action="<?= base_url('users/delete/' . $u['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Se eliminaran sus registros asociados. Continuar?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editUser<?= $u['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="<?= base_url('users/update/' . $u['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Editar Perfil Laboral</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-8 mb-3">
                                                        <label>Nombre</label>
                                                        <input type="text" name="name" value="<?= esc($u['name']) ?>" class="form-control" required>
                                                    </div>
                                                    <div class="col-4 mb-3">
                                                        <label>Documento</label>
                                                        <input type="text" name="document_id" value="<?= esc($u['document_id']) ?>" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Rol del Sistema</label>
                                                    <select name="role" class="form-select">
                                                        <option value="employee" <?= $u['role'] == 'employee' ? 'selected' : '' ?>>Empleado/Operario</option>
                                                        <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                                    </select>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <label>Departamento</label>
                                                        <select name="department_id" class="form-select">
                                                            <option value="">Ninguno</option>
                                                            <?php foreach ($departments as $d): ?>
                                                                <option value="<?= $d['id'] ?>" <?= $u['department_id'] == $d['id'] ? 'selected' : '' ?>><?= esc($d['name']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label>Contrato</label>
                                                        <select name="employee_type" class="form-select">
                                                            <option value="Permanente" <?= $u['employee_type'] == 'Permanente' ? 'selected' : '' ?>>Permanente</option>
                                                            <option value="Temporal" <?= $u['employee_type'] == 'Temporal' ? 'selected' : '' ?>>Temporal</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label>Salario</label>
                                                        <input type="number" step="0.01" name="salary_base" value="<?= $u['salary_base'] ?>" class="form-control">
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label>Fecha de Alta</label>
                                                        <input type="date" name="hire_date" value="<?= $u['hire_date'] ?>" class="form-control">
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                            <label>Resetear Contraseña (dejar vacío para no cambiar)</label>
                                                    <input type="password" name="password" class="form-control">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
