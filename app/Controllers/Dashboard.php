<?php

namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;

class Dashboard extends BaseController
{
    public function index()
    {
        if (session()->get('role') == 'admin') {
            $attendanceModel = new Attendance();

            $workingNow = $attendanceModel->select('attendance.*, users.name')
                ->join('users', 'users.id = attendance.user_id')
                ->where('check_out IS NULL')
                ->findAll();

            $firstDayOfMonth = date('Y-m-01 00:00:00');
            $lastDayOfMonth = date('Y-m-t 23:59:59');

            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT users.name, users.id, COUNT(shifts.id) as total_shifts
                FROM users
                LEFT JOIN shifts ON users.id = shifts.user_id AND shifts.start_time >= ? AND shifts.start_time <= ?
                WHERE users.role != 'admin'
                GROUP BY users.id
                ORDER BY total_shifts DESC
            ", [$firstDayOfMonth, $lastDayOfMonth]);
            $shiftsPerEmployee = $query->getResultArray();

            $overworked = [];
            $underworked = [];
            foreach ($shiftsPerEmployee as $se) {
                if ($se['total_shifts'] > 6) {
                    $overworked[] = $se;
                } elseif ($se['total_shifts'] < 2) {
                    $underworked[] = $se;
                }
            }

            $userModel = new User();
            $users = $userModel->where('role !=', 'admin')->findAll();

            return view('dashboard/admin', [
                'workingNow' => $workingNow,
                'shiftsPerEmployee' => $shiftsPerEmployee,
                'overworked' => $overworked,
                'underworked' => $underworked,
                'users' => $users,
            ]);
        }

        $userId = (int) session()->get('user_id');
        $shiftModel = new Shift();
        $attendanceModel = new Attendance();

        $nextShift = $shiftModel->select('shifts.*, schedules.name as schedule_name')
            ->join('schedules', 'schedules.id = shifts.schedule_id', 'left')
            ->where('shifts.user_id', $userId)
            ->where('shifts.is_active', 1)
            ->where('shifts.status', 'approved')
            ->where('shifts.start_time >=', date('Y-m-d H:i:s'))
            ->orderBy('shifts.start_time', 'ASC')
            ->first();

        $activeAttendance = $attendanceModel->where('user_id', $userId)
            ->where('check_out', null)
            ->orderBy('check_in', 'DESC')
            ->first();

        return view('dashboard/index', [
            'nextShift' => $nextShift,
            'activeAttendance' => $activeAttendance,
        ]);
    }

    public function getShiftsEvents()
    {
        if (session()->get('role') != 'admin') {
            return $this->response->setJSON([]);
        }

        $shiftModel = new Shift();
        $userModel = new User();
        $shifts = $shiftModel->findAll();

        $userMap = [];
        foreach ($userModel->findAll() as $u) {
            $userMap[$u['id']] = $u['name'];
        }

        $events = [];
        foreach ($shifts as $s) {
            if ((int) ($s['is_active'] ?? 1) !== 1) {
                continue;
            }

            $isAssigned = !empty($s['user_id']);
            $title = $isAssigned ? ($userMap[$s['user_id']] ?? 'Asignado') : 'Turno Abierto';
            $color = ($s['status'] ?? 'pending') === 'approved'
                ? ($isAssigned ? '#28a745' : '#198754')
                : '#ffc107';

            $events[] = [
                'id' => $s['id'],
                'title' => $title,
                'start' => $s['start_time'],
                'end' => $s['end_time'],
                'color' => $color,
                'extendedProps' => ['isAssigned' => $isAssigned, 'status' => $s['status'] ?? 'pending'],
            ];
        }

        return $this->response->setJSON($events);
    }

    public function assignShift()
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $shiftId = (int) $this->request->getPost('shift_id');
        $userId = (int) $this->request->getPost('user_id');
        $shiftModel = new Shift();
        $shift = $shiftModel->find($shiftId);
        if (!$shift) {
            return redirect()->back()->with('error', 'El turno seleccionado no existe.');
        }

        if (!empty($shift['user_id'])) {
            return redirect()->back()->with('error', 'El turno ya fue asignado.');
        }

        $minAllowedDate = date('Y-m-d', strtotime('+2 days'));
        if (date('Y-m-d', strtotime($shift['start_time'])) < $minAllowedDate) {
            return redirect()->back()->with('error', 'Solo puedes asignar turnos programados con al menos 2 días de anticipación.');
        }

        $user = (new User())->find($userId);
        if (!$user || $user['role'] === 'admin') {
            return redirect()->back()->with('error', 'El empleado seleccionado no es válido.');
        }

        if ($this->userHasApprovedAbsenceInRange($userId, $shift['start_time'], $shift['end_time'])) {
            return redirect()->back()->with('error', 'El empleado seleccionado tiene una licencia aprobada en ese período.');
        }

        $shiftModel->update($shiftId, [
            'user_id' => $userId,
            'status' => 'pending',
            'approved_at' => null,
        ]);

        $this->createAnnouncement(
            'Nuevo turno asignado',
            sprintf('Se te asignó un turno para el %s.', date('d/m/Y H:i', strtotime($shift['start_time']))),
            (int) session()->get('user_id'),
            $userId,
            null,
            'shift_assignment'
        );

        return redirect()->back()->with('success', 'Turno asignado correctamente via calendario.');
    }
}
