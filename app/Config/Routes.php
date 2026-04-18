<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');

$routes->get('/', [\App\Controllers\Auth::class, 'login']);
$routes->get('/auth/login', [\App\Controllers\Auth::class, 'login']);
$routes->post('/auth/login', [\App\Controllers\Auth::class, 'login']);
$routes->post('/auth/logout', [\App\Controllers\Auth::class, 'logout'], ['filter' => 'auth']);

$routes->get('/dashboard', [\App\Controllers\Dashboard::class, 'index'], ['filter' => 'auth']);

$routes->get('/attendance', [\App\Controllers\Attendance::class, 'index'], ['filter' => 'auth']);
$routes->get('/attendance/scan', [\App\Controllers\Attendance::class, 'scan'], ['filter' => 'auth']);
$routes->get('/attendance/qr/(:segment)', 'Attendance::qrEntry/$1');
$routes->post('/attendance/checkin', [\App\Controllers\Attendance::class, 'checkin'], ['filter' => 'auth']);
$routes->post('/attendance/checkin-qr', [\App\Controllers\Attendance::class, 'checkinQr'], ['filter' => 'auth']);
$routes->post('/attendance/checkout', [\App\Controllers\Attendance::class, 'checkout'], ['filter' => 'auth']);

$routes->get('/objectives', [\App\Controllers\Objectives::class, 'index'], ['filter' => 'auth']);
$routes->post('/objectives/store', [\App\Controllers\Objectives::class, 'store'], ['filter' => 'auth']);
$routes->post('/objectives/assign', [\App\Controllers\Objectives::class, 'assign'], ['filter' => 'auth']);
$routes->post('/objectives/update_progress/(:num)', [\App\Controllers\Objectives::class, 'update_progress/$1'], ['filter' => 'auth']);

$routes->get('/performance', [\App\Controllers\Performance::class, 'index'], ['filter' => 'auth']);
$routes->post('/performance/create_campaign', [\App\Controllers\Performance::class, 'create_campaign'], ['filter' => 'auth']);
$routes->post('/performance/submit_feedback', [\App\Controllers\Performance::class, 'submit_feedback'], ['filter' => 'auth']);
$routes->post('/performance/close_calculation/(:num)', [\App\Controllers\Performance::class, 'close_calculation/$1'], ['filter' => 'auth']);

$routes->get('/announcements', [\App\Controllers\Announcements::class, 'index'], ['filter' => 'auth']);
$routes->post('/announcements/store', [\App\Controllers\Announcements::class, 'store'], ['filter' => 'auth']);

$routes->get('/qr-points', [\App\Controllers\QrPoints::class, 'index'], ['filter' => 'auth']);
$routes->post('/qr-points/store', [\App\Controllers\QrPoints::class, 'store'], ['filter' => 'auth']);
$routes->post('/qr-points/update/(:num)', 'QrPoints::update/$1', ['filter' => 'auth']);
$routes->post('/qr-points/toggle/(:num)', 'QrPoints::toggle/$1', ['filter' => 'auth']);
$routes->post('/qr-points/regenerate/(:num)', 'QrPoints::regenerate/$1', ['filter' => 'auth']);
$routes->post('/qr-points/delete/(:num)', 'QrPoints::delete/$1', ['filter' => 'auth']);

$routes->get('/shifts', [\App\Controllers\Shifts::class, 'index'], ['filter' => 'auth']);
$routes->get('/shifts/admin', [\App\Controllers\Shifts::class, 'admin'], ['filter' => 'auth']);
$routes->post('/shifts/store', [\App\Controllers\Shifts::class, 'store'], ['filter' => 'auth']);
$routes->post('/shifts/update/(:num)', [\App\Controllers\Shifts::class, 'update/$1'], ['filter' => 'auth']);
$routes->post('/shifts/toggle/(:num)', [\App\Controllers\Shifts::class, 'toggle/$1'], ['filter' => 'auth']);
$routes->post('/shifts/approve/(:num)', [\App\Controllers\Shifts::class, 'approve/$1'], ['filter' => 'auth']);

$routes->get('/users', [\App\Controllers\Users::class, 'index'], ['filter' => 'auth']);
$routes->post('/users/store', [\App\Controllers\Users::class, 'store'], ['filter' => 'auth']);
$routes->post('/users/update/(:num)', [\App\Controllers\Users::class, 'update/$1'], ['filter' => 'auth']);
$routes->post('/users/delete/(:num)', [\App\Controllers\Users::class, 'delete/$1'], ['filter' => 'auth']);
$routes->post('/users/toggle_status/(:num)', [\App\Controllers\Users::class, 'toggleStatus/$1'], ['filter' => 'auth']);

$routes->get('/schedules', [\App\Controllers\Schedules::class, 'index'], ['filter' => 'auth']);
$routes->post('/schedules/store', [\App\Controllers\Schedules::class, 'store'], ['filter' => 'auth']);
$routes->post('/schedules/update/(:num)', [\App\Controllers\Schedules::class, 'update/$1'], ['filter' => 'auth']);
$routes->post('/schedules/delete/(:num)', [\App\Controllers\Schedules::class, 'delete/$1'], ['filter' => 'auth']);

$routes->get('/departments', [\App\Controllers\Departments::class, 'index'], ['filter' => 'auth']);
$routes->post('/departments/store', [\App\Controllers\Departments::class, 'store'], ['filter' => 'auth']);
$routes->post('/departments/update/(:num)', [\App\Controllers\Departments::class, 'update/$1'], ['filter' => 'auth']);
$routes->post('/departments/delete/(:num)', [\App\Controllers\Departments::class, 'delete/$1'], ['filter' => 'auth']);

$routes->get('/dashboard/get_shifts_events', [\App\Controllers\Dashboard::class, 'getShiftsEvents'], ['filter' => 'auth']);
$routes->post('/dashboard/assign_shift', [\App\Controllers\Dashboard::class, 'assignShift'], ['filter' => 'auth']);

$routes->get('/absences', [\App\Controllers\Absences::class, 'index'], ['filter' => 'auth']);
$routes->post('/absences/store', [\App\Controllers\Absences::class, 'store'], ['filter' => 'auth']);
$routes->post('/absences/update_status/(:num)', [\App\Controllers\Absences::class, 'updateStatus/$1'], ['filter' => 'auth']);
$routes->get('/absences/attachment/(:num)', [\App\Controllers\Absences::class, 'attachment/$1'], ['filter' => 'auth']);
$routes->get('/absences/preview/(:num)', [\App\Controllers\Absences::class, 'preview/$1'], ['filter' => 'auth']);
