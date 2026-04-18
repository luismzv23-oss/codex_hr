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
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Solicitante</th>
                                <th>Tipo / Motivo</th>
                                <th>Período</th>
                                <th>Documento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absences as $a): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($a['name']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= esc($a['type']) ?></span><br>
                                    <small class="text-muted"><?= esc($a['reason'] ?? 'Sin observaciones') ?></small>
                                </td>
                                <td><?= date('d/m/Y', strtotime($a['start_date'])) ?> al <?= date('d/m/Y', strtotime($a['end_date'])) ?></td>
                                <td class="text-center">
                                    <?php if ($a['attachment']): ?>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary preview-document"
                                                data-preview-url="<?= base_url('absences/preview/' . $a['id']) ?>"
                                                data-download-url="<?= base_url('absences/attachment/' . $a['id']) ?>"
                                                data-title="Documento de <?= esc($a['name'], 'attr') ?>"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="<?= base_url('absences/attachment/' . $a['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Descargar">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($a['status'] == 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php elseif ($a['status'] == 'Aprobado'): ?>
                                        <span class="badge bg-success">Aprobado</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rechazado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
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
</div>

<div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="documentPreviewTitle">Vista previa del documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 75vh;">
                <iframe id="documentPreviewFrame" src="" class="w-100 h-100 border-0" style="min-height: 75vh;"></iframe>
            </div>
            <div class="modal-footer">
                <a href="#" id="documentDownloadLink" class="btn btn-outline-secondary">
                    <i class="bi bi-download"></i> Descargar
                </a>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('documentPreviewModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
    const frame = document.getElementById('documentPreviewFrame');
    const title = document.getElementById('documentPreviewTitle');
    const downloadLink = document.getElementById('documentDownloadLink');

    document.querySelectorAll('.preview-document').forEach((button) => {
        button.addEventListener('click', function () {
            if (!modal || !frame || !title || !downloadLink) {
                return;
            }

            frame.src = this.dataset.previewUrl;
            title.textContent = this.dataset.title || 'Vista previa del documento';
            downloadLink.href = this.dataset.downloadUrl || '#';
            modal.show();
        });
    });

    modalElement?.addEventListener('hidden.bs.modal', function () {
        if (frame) {
            frame.src = '';
        }
    });
});
</script>
<?= $this->endSection() ?>
