<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Organigrama Empresarial<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Estructura Organizativa</h6>
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addDeptModal">
                    <i class="bi bi-plus-lg"></i> Nuevo Departamento
                </button>
            </div>
            <div class="card-body overflow-auto text-center" style="min-height: 400px; background-color:#fcfcfc;">
                <div id="chart_div"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header border-bottom bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Gestión de Áreas</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Departamento</th>
                            <th>Subordinado de</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $d): ?>
                        <tr>
                            <td><?= $d['id'] ?></td>
                            <td class="fw-bold"><?= esc($d['name']) ?></td>
                            <td>
                                <?php
                                if ($d['parent_id']) {
                                    $parent = array_filter($departments, fn($x) => $x['id'] == $d['parent_id']);
                                    echo esc(reset($parent)['name'] ?? 'Desconocido');
                                } else {
                                    echo 'Raíz';
                                }
                                ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $d['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <form action="<?= base_url('departments/delete/' . $d['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Seguro que deseas eliminar el area?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?= $d['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= base_url('departments/update/' . $d['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title text-dark">Editar Departamento</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Nombre</label>
                                                <input type="text" name="name" value="<?= esc($d['name']) ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Subordinado de</label>
                                                <select name="parent_id" class="form-select">
                                                    <option value="">(Nivel Máximo)</option>
                                                    <?php foreach ($departments as $parentOps): ?>
                                                        <?php if ($parentOps['id'] != $d['id']): ?>
                                                        <option value="<?= $parentOps['id'] ?>" <?= $d['parent_id'] == $parentOps['id'] ? 'selected' : '' ?>><?= esc($parentOps['name']) ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('departments/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Agregar Departamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Subordinado de</label>
                        <select name="parent_id" class="form-select">
                            <option value="">(Nivel Máximo)</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= esc($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load('current', { packages: ['orgchart'] });
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Name');
    data.addColumn('string', 'Manager');
    data.addColumn('string', 'ToolTip');

    data.addRows([
        <?php foreach ($departments as $d): ?>
        [
            { v: '<?= $d['id'] ?>', f: '<div style="font-size:16px; font-weight:bold; padding:5px;"><?= esc($d['name']) ?></div>' },
            '<?= $d['parent_id'] ? $d['parent_id'] : '' ?>',
            ''
        ],
        <?php endforeach; ?>
    ]);

    var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
    chart.draw(data, { allowHtml: true, size: 'large' });
}
</script>
<?= $this->endSection() ?>
