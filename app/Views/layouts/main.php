<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codex HR - <?= $this->renderSection('title') ?? 'Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 10px 15px; display: block; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #495057; }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php if (session()->get('isLoggedIn')): ?>
        <nav class="sidebar p-3" style="width: 250px;">
            <h4 class="text-white mb-4"><i class="bi bi-buildings"></i> Codex HR</h4>
            <a href="<?= base_url('dashboard') ?>" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <?php if (session()->get('role') == 'admin'): ?>
                <a href="<?= base_url('users') ?>"><i class="bi bi-people"></i> Personal y Legajo</a>
                <a href="<?= base_url('departments') ?>"><i class="bi bi-diagram-3"></i> Departamentos</a>
                <a href="<?= base_url('schedules') ?>"><i class="bi bi-clock"></i> Horarios Base</a>
                <a href="<?= base_url('shifts/admin') ?>"><i class="bi bi-calendar-range"></i> Turnos</a>
                <a href="<?= base_url('qr-points') ?>"><i class="bi bi-qr-code-scan"></i> Puntos QR</a>
                <hr class="text-white">
                <a href="<?= base_url('objectives') ?>"><i class="bi bi-bullseye"></i> KPIs de Empresa</a>
                <a href="<?= base_url('performance') ?>"><i class="bi bi-bar-chart-line-fill"></i> Campañas de Desempeño</a>
                <hr class="text-white">
            <?php else: ?>
                <a href="<?= base_url('shifts') ?>"><i class="bi bi-calendar"></i> Mis Turnos</a>
                <a href="<?= base_url('attendance/scan') ?>"><i class="bi bi-qr-code-scan"></i> Escanear QR</a>
                <a href="<?= base_url('performance') ?>"><i class="bi bi-card-checklist"></i> Desempeño 360</a>
            <?php endif; ?>
            <a href="<?= base_url('attendance') ?>"><i class="bi bi-check2-circle"></i> Asistencia</a>
            <a href="<?= base_url('absences') ?>"><i class="bi bi-airplane"></i> Mis Licencias</a>
            <a href="<?= base_url('announcements') ?>"><i class="bi bi-megaphone"></i> Avisos</a>
            <hr class="text-white">
            <form action="<?= base_url('auth/logout') ?>" method="POST" class="mt-2">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">
                    <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                </button>
            </form>
        </nav>
        <?php endif; ?>

        <main class="flex-grow-1">
            <?php if (session()->get('isLoggedIn')): ?>
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1"><?= $this->renderSection('title') ?? 'Dashboard' ?></span>
                    <div class="d-flex align-items-center">
                        <span class="me-3"><i class="bi bi-person-circle"></i> <?= session()->get('name') ?> (<?= ucfirst(session()->get('role')) ?>)</span>
                    </div>
                </div>
            </nav>
            <?php endif; ?>

            <div class="container p-4">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
