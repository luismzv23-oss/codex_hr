<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Portal de Desempeño y Feedback Global<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-5">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white"><i class="bi bi-card-checklist"></i> Mis Objetivos</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($objectives as $obj): ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold"><?= esc($obj['title']) ?></h6>
                            <span class="badge bg-<?= $obj['status'] == 'Cumplido' ? 'success' : 'warning text-dark' ?>"><?= $obj['status'] ?></span>
                        </div>
                        <p class="text-muted small mb-2"><?= esc($obj['description']) ?></p>
                        <form action="<?= base_url('objectives/update_progress/' . $obj['id']) ?>" method="POST" class="d-flex align-items-center">
                            <?= csrf_field() ?>
                            <input type="range" class="form-range flex-grow-1 me-2" name="progress" min="0" max="100" value="<?= $obj['progress'] ?>" oninput="this.nextElementSibling.value = this.value + '%'">
                            <output class="fw-bold me-2" style="width: 40px;"><?= $obj['progress'] ?>%</output>
                            <button class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-save2"></i> Update</button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($objectives)): ?>
                    <li class="list-group-item text-muted text-center p-4">RRHH no ha enlazado metas KPI a tu perfil.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow h-100 border-left-warning">
            <div class="card-header bg-dark text-white"><i class="bi bi-people-fill"></i> Feedback 360</div>
            <div class="card-body bg-light">
                <div class="alert alert-warning border-0">
                    <strong>Pilar de Cultura Empresarial:</strong> selecciona una campaña abierta. Puedes autoevaluarte o valorar a otra persona habilitada.
                </div>

                <form action="<?= base_url('performance/submit_feedback') ?>" method="POST" class="bg-white p-4 shadow-sm rounded">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Campaña Vigente</label>
                            <select name="evaluation_id" class="form-select" required>
                                <option value="">(Selecciona Período)</option>
                                <?php foreach ($evaluations as $ev): ?>
                                    <?php if ($ev['status'] == 'Abierta'): ?>
                                        <option value="<?= $ev['id'] ?>"><?= esc($ev['name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-6 mb-3">
                            <label>Tipo de Relación</label>
                            <select name="rater_type" class="form-select" required>
                                <option value="Autoevaluacion">Mi Autoevaluación</option>
                                <option value="Companero">Feedback de Par / Compañero</option>
                                <option value="Liderazgo">Feedback Ascendente</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>¿A quién estás valorando?</label>
                        <select name="evaluatee_id" class="form-select" required>
                            <option value="<?= session()->get('user_id') ?>" class="text-primary fw-bold">Yo mismo (<?= session()->get('name') ?>)</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Calificación de Desempeño</label>
                        <select name="score" class="form-select" required>
                            <option value="5">5 - Excede ampliamente las expectativas</option>
                            <option value="4">4 - Desempeño sólido y cumplidor</option>
                            <option value="3">3 - Desempeño promedio</option>
                            <option value="2">2 - Por debajo de las expectativas</option>
                            <option value="1">1 - Rendimiento insatisfactorio</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Comentarios</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>

                    <button class="btn btn-warning w-100 fw-bold btn-lg shadow-sm">Firmar y Enviar Evaluación 360</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
