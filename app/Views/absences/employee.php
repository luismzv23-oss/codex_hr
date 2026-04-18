<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Mis Licencias y Vacaciones<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">Solicitar Licencia</div>
            <div class="card-body">
                <form action="<?= base_url('absences/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Tipo de Ausencia</label>
                        <select name="type" class="form-select" required>
                            <option value="Vacaciones">Vacaciones</option>
                            <option value="Licencia Médica">Licencia Médica</option>
                            <option value="Licencia por Paternidad/Maternidad">Licencia por Paternidad/Maternidad</option>
                            <option value="Asuntos Personales">Asuntos Personales</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Desde</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Hasta</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Observaciones</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Adjuntar Documento (PDF, JPG o PNG - máx. 5 MB)</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,image/png,image/jpeg,application/pdf">
                    </div>
                    <button class="btn btn-success w-100"><i class="bi bi-send"></i> Enviar Solicitud</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Historial de Solicitudes</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Fechas</th>
                                <th>Estado</th>
                                <th>Adjunto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absences as $a): ?>
                            <tr>
                                <td><?= esc($a['type']) ?></td>
                                <td><?= date('d/m/Y', strtotime($a['start_date'])) ?> - <?= date('d/m/Y', strtotime($a['end_date'])) ?></td>
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
                                    <?php if ($a['attachment']): ?>
                                        <div class="d-flex gap-2">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary preview-document"
                                                data-preview-url="<?= base_url('absences/preview/' . $a['id']) ?>"
                                                data-download-url="<?= base_url('absences/attachment/' . $a['id']) ?>"
                                                data-title="Mi documento adjunto"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="<?= base_url('absences/attachment/' . $a['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">No aplica</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($absences)): ?>
                                <tr><td colspan="4" class="text-center text-muted">Aún no has solicitado licencias.</td></tr>
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
