<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Panel Analítico 360°<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-success shadow h-100 py-2 border-top border-success border-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-success small fw-bold mb-1">Trabajando Ahora</div>
                        <div class="h4 mb-0"><?= count($workingNow) ?> Empleados</div>
                    </div>
                    <i class="bi bi-clock fs-2 text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h6 class="m-0 fw-bold text-primary">Turnos Cubiertos en el Mes</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Empleado</th>
                                <th>Total Turnos</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shiftsPerEmployee as $se): ?>
                                <tr>
                                    <td><?= esc($se['name']) ?></td>
                                    <td><?= $se['total_shifts'] ?></td>
                                    <td>
                                        <?php if ($se['total_shifts'] > 6): ?>
                                            <span class="badge bg-danger">Sobrecarga</span>
                                        <?php elseif ($se['total_shifts'] < 2): ?>
                                            <span class="badge bg-warning text-dark">Baja carga</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Óptimo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($shiftsPerEmployee)): ?>
                                <tr><td colspan="3" class="text-center text-muted">Sin datos.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card shadow h-100">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 fw-bold">Alerta: Sobrecarga</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($overworked)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($overworked as $o): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <?= esc($o['name']) ?>
                                <span class="badge bg-danger rounded-pill"><?= $o['total_shifts'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-success m-0"><i class="bi bi-check-circle"></i> Ningún empleado sobrecargado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card shadow h-100">
            <div class="card-header bg-warning">
                <h6 class="m-0 fw-bold text-dark">Baja Asignación</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($underworked)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($underworked as $u): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <?= esc($u['name']) ?>
                                <span class="badge bg-warning text-dark rounded-pill"><?= $u['total_shifts'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-success m-0"><i class="bi bi-check-circle"></i> Todos tienen suficientes turnos.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h6 class="m-0 fw-bold text-success">Personal Activo en este momento</h6>
            </div>
            <div class="card-body">
                <?php if (empty($workingNow)): ?>
                    <div class="alert alert-secondary">Nadie ha realizado check-in hoy que siga activo.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Hora Ingreso</th>
                                    <th>Tiempo Activo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($workingNow as $w): ?>
                                <tr>
                                    <td><?= esc($w['name']) ?></td>
                                    <td><?= date('H:i', strtotime($w['check_in'])) ?></td>
                                    <td>
                                        <?php
                                            $ingreso = new DateTime($w['check_in']);
                                            $ahora = new DateTime();
                                            $diff = $ingreso->diff($ahora);
                                            echo $diff->format('%h horas, %i min');
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h6 class="m-0 fw-bold">Calendario Interactivo de Turnos</h6>
                <span><i class="bi bi-calendar-week"></i></span>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('dashboard/assign_shift') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Asignar Turno Libre</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="shift_id" id="modal_shift_id">
                    <p>Has seleccionado un <strong>Turno Abierto</strong>. Elige un operario para cubrirlo.</p>
                    <select name="user_id" class="form-select" required>
                        <option value="">Seleccione personal...</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Asignar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales-all.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      locale: 'es',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: '<?= base_url("dashboard/get_shifts_events") ?>',
      eventClick: function(info) {
          if(!info.event.extendedProps.isAssigned) {
              document.getElementById('modal_shift_id').value = info.event.id;
              var myModal = new bootstrap.Modal(document.getElementById('assignShiftModal'));
              myModal.show();
          } else {
              alert('Turno cubierto por: ' + info.event.title);
          }
      }
    });
    calendar.render();
});
</script>
<?= $this->endSection() ?>
