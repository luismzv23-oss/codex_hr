<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Módulo de Objetivos (KPIs)<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white"><i class="bi bi-bullseye"></i> Crear Nueva Meta</div>
            <div class="card-body">
                <form action="<?= base_url('objectives/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Título del Objetivo</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select name="type" class="form-select">
                            <option value="KPI Cuantitativo">KPI Cuantitativo</option>
                            <option value="Metas Cualitativas">Metas Cualitativas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Peso Relativo (%)</label>
                        <input type="number" step="0.01" name="weight" value="10" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Desde</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Limite</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <button class="btn btn-warning w-100 fw-bold">Guardar Objetivo</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-white font-weight-bold text-primary"><i class="bi bi-diagram-2"></i> Delegar a Empleados</div>
            <div class="card-body">
                <form action="<?= base_url('objectives/assign') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <select name="objective_id" class="form-select" required>
                                <option value="">(Selecciona un Objetivo)</option>
                                <?php foreach ($objectives as $o): ?>
                                    <option value="<?= $o['id'] ?>"><?= esc($o['title']) ?> (Peso: <?= $o['weight'] ?>%)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select name="user_ids[]" class="form-select" multiple required>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Ctrl+Click para seleccionar varios</small>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Asignar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-white font-weight-bold text-primary">Operarios con Metas Activas</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Empleado</th>
                            <th>Objetivo</th>
                            <th>Peso</th>
                            <th>Progreso</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $a): ?>
                        <tr>
                            <td class="fw-bold"><?= esc($a['name']) ?></td>
                            <td><?= esc($a['title']) ?></td>
                            <td><?= esc($a['weight']) ?>%</td>
                            <td class="align-middle">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?= $a['progress'] == 100 ? 'success' : 'info' ?>" role="progressbar" style="width: <?= $a['progress'] ?>%;">
                                        <?= $a['progress'] ?>%
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-<?= $a['status'] == 'Cumplido' ? 'success' : 'warning text-dark' ?>"><?= $a['status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($assignments)): ?>
                            <tr><td colspan="5" class="text-center text-muted">Ninguna meta asignada por ahora.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
