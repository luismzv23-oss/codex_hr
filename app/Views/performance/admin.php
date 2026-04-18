<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Módulo de Desempeño y Feedback 360<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white"><i class="bi bi-calendar-event"></i> Apertura de Campaña 360</div>
            <div class="card-body">
                <form action="<?= base_url('performance/create_campaign') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Nombre del Ciclo de Evaluación</label>
                        <input type="text" name="name" class="form-control" required>
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
                    <button class="btn btn-warning w-100 fw-bold">Inaugurar Período Evaluativo</button>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-white font-weight-bold text-primary">Campañas Históricas</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($evaluations as $ev): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= esc($ev['name']) ?></strong><br>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($ev['start_date'])) ?> - <?= date('d/m/Y', strtotime($ev['end_date'])) ?></small>
                            </div>
                            <?php if ($ev['status'] == 'Abierta'): ?>
                                <form action="<?= base_url('performance/close_calculation/' . $ev['id']) ?>" method="POST" onsubmit="return confirm('Se cerrará la campaña y se ejecutará el cálculo final. ¿Deseas continuar?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm">Cerrar y Calcular</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success"><i class="bi bi-lock-fill"></i> Cerrada</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($evaluations)): ?>
                        <li class="list-group-item text-center text-muted">No existen campañas de evaluación de desempeño.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow h-100">
            <div class="card-header bg-white font-weight-bold text-primary d-flex align-items-center justify-content-between">
                <span><i class="bi bi-bar-chart-fill"></i> Resultados Finales</span>
            </div>
            <div class="card-body p-0">
                <div class="alert alert-info border-0 rounded-0 m-0">
                    <small><i class="bi bi-info-circle"></i> Aquí aparecerán las calificaciones finales de las campañas cerradas.</small>
                </div>
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Ciclo</th>
                            <th>Empleado Evaluado</th>
                            <th>Nota Final</th>
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $res): ?>
                        <tr>
                            <td class="align-middle fw-bold"><?= esc($res['eval_name']) ?></td>
                            <td class="align-middle"><?= esc($res['user_name']) ?></td>
                            <td class="align-middle text-center h5">
                                <?php if ($res['total_score'] >= 80): ?>
                                    <span class="badge bg-success"><?= $res['total_score'] ?> / 100</span>
                                <?php elseif ($res['total_score'] >= 50): ?>
                                    <span class="badge bg-warning text-dark"><?= $res['total_score'] ?> / 100</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?= $res['total_score'] ?> / 100</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-muted small"><?= esc($res['comments']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($results)): ?>
                            <tr><td colspan="4" class="text-center text-muted p-4">No se han procesado calificaciones finales aun.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
