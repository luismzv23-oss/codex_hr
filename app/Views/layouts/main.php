<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codex HR - <?= $this->renderSection('title') ?? 'Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #1f2937;
            --sidebar-hover: #374151;
            --sidebar-text: #cbd5e1;
            --app-bg: #f8fafc;
        }

        body {
            background-color: var(--app-bg);
        }

        .app-shell {
            min-height: 100vh;
        }

        .app-sidebar {
            width: 270px;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #111827 100%);
        }

        .app-sidebar .nav-link,
        .app-sidebar a {
            color: var(--sidebar-text);
            text-decoration: none;
            padding: 0.8rem 1rem;
            border-radius: 0.75rem;
            display: block;
        }

        .app-sidebar .nav-link:hover,
        .app-sidebar .nav-link.active,
        .app-sidebar a:hover,
        .app-sidebar a.active {
            color: #fff;
            background-color: var(--sidebar-hover);
        }

        .content-wrap {
            min-width: 0;
        }

        .page-container {
            width: min(100%, 1320px);
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 991.98px) {
            .page-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell d-lg-flex">
        <?php if (session()->get('isLoggedIn')): ?>
            <aside class="app-sidebar text-white p-3 d-none d-lg-flex flex-column">
                <h4 class="mb-4"><i class="bi bi-buildings"></i> Codex HR</h4>
                <nav class="d-flex flex-column gap-2">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <?php if (session()->get('role') == 'admin'): ?>
                        <a href="<?= base_url('users') ?>" class="nav-link"><i class="bi bi-people"></i> Personal y Legajo</a>
                        <a href="<?= base_url('departments') ?>" class="nav-link"><i class="bi bi-diagram-3"></i> Departamentos</a>
                        <a href="<?= base_url('schedules') ?>" class="nav-link"><i class="bi bi-clock"></i> Horarios Base</a>
                        <a href="<?= base_url('shifts/admin') ?>" class="nav-link"><i class="bi bi-calendar-range"></i> Turnos</a>
                        <a href="<?= base_url('qr-points') ?>" class="nav-link"><i class="bi bi-qr-code-scan"></i> Códigos QR</a>
                        <hr class="text-white-50">
                        <a href="<?= base_url('objectives') ?>" class="nav-link"><i class="bi bi-bullseye"></i> KPIs de Empresa</a>
                        <a href="<?= base_url('performance') ?>" class="nav-link"><i class="bi bi-bar-chart-line-fill"></i> Campañas de Desempeño</a>
                        <hr class="text-white-50">
                    <?php else: ?>
                        <a href="<?= base_url('shifts') ?>" class="nav-link"><i class="bi bi-calendar"></i> Mis Turnos</a>
                        <a href="<?= base_url('attendance/scan') ?>" class="nav-link"><i class="bi bi-qr-code-scan"></i> Escanear QR</a>
                        <a href="<?= base_url('performance') ?>" class="nav-link"><i class="bi bi-card-checklist"></i> Desempeño 360</a>
                    <?php endif; ?>
                    <a href="<?= base_url('attendance') ?>" class="nav-link"><i class="bi bi-check2-circle"></i> Asistencia</a>
                    <a href="<?= base_url('absences') ?>" class="nav-link"><i class="bi bi-airplane"></i> Mis Licencias</a>
                    <a href="<?= base_url('announcements') ?>" class="nav-link"><i class="bi bi-megaphone"></i> Avisos</a>
                </nav>
                <div class="mt-auto pt-3">
                    <form action="<?= base_url('auth/logout') ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </aside>
        <?php endif; ?>

        <main class="content-wrap flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3 px-md-4">
                <div class="container-fluid page-container">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <button class="btn btn-outline-secondary d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                            <i class="bi bi-list"></i>
                        </button>
                    <?php endif; ?>
                    <span class="navbar-brand mb-0 h1"><?= $this->renderSection('title') ?? 'Dashboard' ?></span>
                    <?php if (session()->get('isLoggedIn')): ?>
                        <div class="ms-auto small text-muted text-end">
                            <div><i class="bi bi-person-circle"></i> <?= session()->get('name') ?></div>
                            <div><?= ucfirst(session()->get('role')) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="container-fluid px-3 px-md-4 py-4">
                <div class="mx-auto page-container">
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
            </div>
        </main>
    </div>

    <?php if (session()->get('isLoggedIn')): ?>
        <div class="offcanvas offcanvas-start app-sidebar text-white" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobileSidebarLabel"><i class="bi bi-buildings"></i> Codex HR</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <nav class="d-flex flex-column gap-2">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <?php if (session()->get('role') == 'admin'): ?>
                        <a href="<?= base_url('users') ?>" class="nav-link"><i class="bi bi-people"></i> Personal y Legajo</a>
                        <a href="<?= base_url('departments') ?>" class="nav-link"><i class="bi bi-diagram-3"></i> Departamentos</a>
                        <a href="<?= base_url('schedules') ?>" class="nav-link"><i class="bi bi-clock"></i> Horarios Base</a>
                        <a href="<?= base_url('shifts/admin') ?>" class="nav-link"><i class="bi bi-calendar-range"></i> Turnos</a>
                        <a href="<?= base_url('qr-points') ?>" class="nav-link"><i class="bi bi-qr-code-scan"></i> Códigos QR</a>
                        <a href="<?= base_url('objectives') ?>" class="nav-link"><i class="bi bi-bullseye"></i> KPIs de Empresa</a>
                        <a href="<?= base_url('performance') ?>" class="nav-link"><i class="bi bi-bar-chart-line-fill"></i> Campañas de Desempeño</a>
                    <?php else: ?>
                        <a href="<?= base_url('shifts') ?>" class="nav-link"><i class="bi bi-calendar"></i> Mis Turnos</a>
                        <a href="<?= base_url('attendance/scan') ?>" class="nav-link"><i class="bi bi-qr-code-scan"></i> Escanear QR</a>
                        <a href="<?= base_url('performance') ?>" class="nav-link"><i class="bi bi-card-checklist"></i> Desempeño 360</a>
                    <?php endif; ?>
                    <a href="<?= base_url('attendance') ?>" class="nav-link"><i class="bi bi-check2-circle"></i> Asistencia</a>
                    <a href="<?= base_url('absences') ?>" class="nav-link"><i class="bi bi-airplane"></i> Mis Licencias</a>
                    <a href="<?= base_url('announcements') ?>" class="nav-link"><i class="bi bi-megaphone"></i> Avisos</a>
                </nav>
                <div class="mt-auto pt-3">
                    <form action="<?= base_url('auth/logout') ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
