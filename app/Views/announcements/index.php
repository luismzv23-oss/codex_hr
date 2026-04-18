<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Tablón de Avisos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (session()->get('role') == 'admin'): ?>
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">Publicar Nuevo Aviso</div>
        <div class="card-body">
            <form action="<?= base_url('announcements/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" required value="<?= esc(old('title', '')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Destino</label>
                        <select name="target_type" id="target_type" class="form-select">
                            <option value="all" <?= old('target_type', 'all') === 'all' ? 'selected' : '' ?>>Todo el personal</option>
                            <option value="specific" <?= old('target_type') === 'specific' ? 'selected' : '' ?>>Empleado específico</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="recipient_wrapper" style="<?= old('target_type') === 'specific' ? '' : 'display:none;' ?>">
                        <label class="form-label">Empleado</label>
                        <select name="recipient_user_id" class="form-select">
                            <option value="">Seleccione...</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= old('recipient_user_id') == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Contenido</label>
                        <textarea name="content" class="form-control" rows="3" required><?= esc(old('content', '')) ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-megaphone"></i> Publicar Aviso
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="card shadow">
    <div class="card-header bg-white">
        <form method="GET" action="<?= base_url('announcements') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoría</label>
                <select name="category" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= ($filters['category'] ?? '') === $value ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (session()->get('role') == 'admin'): ?>
                <div class="col-md-3">
                    <label class="form-label">Destino</label>
                    <select name="recipient" class="form-select">
                        <option value="">Todos</option>
                        <option value="all" <?= ($filters['recipient'] ?? '') === 'all' ? 'selected' : '' ?>>Publicación general</option>
                        <option value="admins" <?= ($filters['recipient'] ?? '') === 'admins' ? 'selected' : '' ?>>Solo administradores</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= ($filters['recipient'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                                <?= esc($user['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="<?= base_url('announcements') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Categoría</th>
                        <th>Aviso</th>
                        <th>Destino</th>
                        <th>Turno</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $a): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $a['category'] === 'absence_notice' ? 'dark' : ($a['category'] === 'lateness' ? 'danger' : ($a['category'] === 'shift_assignment' ? 'primary' : 'secondary')) ?>">
                                    <?= esc($categories[$a['category']] ?? $a['category']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= esc($a['title']) ?></div>
                                <small class="text-muted">Por <?= esc($a['author_name'] ?? 'Sistema') ?></small>
                            </td>
                            <td>
                                <?php if (!empty($a['recipient_name'])): ?>
                                    <?= esc($a['recipient_name']) ?>
                                <?php elseif (($a['recipient_role'] ?? null) === 'admin'): ?>
                                    Administradores
                                <?php else: ?>
                                    Todo el personal
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($a['related_shift_id'])): ?>
                                    <div><?= esc($a['shift_user_name'] ?? 'Sin asignar') ?></div>
                                    <small class="text-muted">
                                        <?= esc($a['schedule_name'] ?: 'Turno libre') ?><br>
                                        <?= date('d/m/Y H:i', strtotime($a['shift_start_time'])) ?> - <?= date('d/m/Y H:i', strtotime($a['shift_end_time'])) ?>
                                    </small>
                                <?php else: ?>
                                    <span class="text-muted">No aplica</span>
                                <?php endif; ?>
                            </td>
                            <td><?= nl2br(esc($a['content'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay avisos para los filtros seleccionados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const targetType = document.getElementById('target_type');
    const recipientWrapper = document.getElementById('recipient_wrapper');

    if (!targetType || !recipientWrapper) {
        return;
    }

    const toggleRecipient = () => {
        recipientWrapper.style.display = targetType.value === 'specific' ? '' : 'none';
    };

    targetType.addEventListener('change', toggleRecipient);
    toggleRecipient();
});
</script>
<?= $this->endSection() ?>
