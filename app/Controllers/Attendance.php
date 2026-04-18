<?php

namespace App\Controllers;

use App\Models\Attendance as AttendanceModel;
use App\Models\QrPoint;
use App\Models\Shift;

class Attendance extends BaseController
{
    public function index()
    {
        $attendanceModel = new AttendanceModel();

        if (session()->get('role') == 'admin') {
            $records = $attendanceModel->select('attendance.*, users.name, shifts.start_time as shift_start_time, shifts.end_time as shift_end_time, qr_points.name as qr_point_name')
                ->join('users', 'users.id = attendance.user_id')
                ->join('shifts', 'shifts.id = attendance.shift_id', 'left')
                ->join('qr_points', 'qr_points.id = attendance.qr_point_id', 'left')
                ->orderBy('check_in', 'DESC')
                ->findAll();
        } else {
            $records = $attendanceModel->select('attendance.*, shifts.start_time as shift_start_time, shifts.end_time as shift_end_time, qr_points.name as qr_point_name')
                ->join('shifts', 'shifts.id = attendance.shift_id', 'left')
                ->join('qr_points', 'qr_points.id = attendance.qr_point_id', 'left')
                ->where('attendance.user_id', session()->get('user_id'))
                ->orderBy('check_in', 'DESC')
                ->findAll();
        }

        $activeShift = $attendanceModel->where('user_id', session()->get('user_id'))
            ->where('check_out', null)
            ->orderBy('check_in', 'DESC')
            ->first();

        $activeQrPoints = [];
        if (session()->get('role') !== 'admin') {
            $activeQrPoints = (new QrPoint())
                ->where('qr_points.user_id', (int) session()->get('user_id'))
                ->where('qr_points.is_active', 1)
                ->orderBy('qr_points.name', 'ASC')
                ->findAll();
        }

        return view('attendance/index', [
            'records' => $records,
            'activeShift' => $activeShift,
            'activeQrPoints' => $activeQrPoints,
        ]);
    }

    public function scan()
    {
        if (session()->get('role') === 'admin') {
            return redirect()->to('/attendance')->with('error', 'El escaneo QR está disponible para personal operativo.');
        }

        return view('attendance/scan', [
            'activeQrPoints' => (new QrPoint())
                ->where('qr_points.user_id', (int) session()->get('user_id'))
                ->where('qr_points.is_active', 1)
                ->orderBy('qr_points.name', 'ASC')
                ->findAll(),
        ]);
    }

    public function qrEntry(string $token)
    {
        $qrPoint = (new QrPoint())
            ->select('qr_points.*, users.name as user_name, users.email as user_email, users.document_id, departments.name as department_name')
            ->join('users', 'users.id = qr_points.user_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('qr_points.token', $token)
            ->where('qr_points.is_active', 1)
            ->where('users.is_active', 1)
            ->first();

        if (!$qrPoint) {
            $target = session()->get('isLoggedIn') ? '/attendance' : '/auth/login';
            return redirect()->to($target)->with('error', 'El código QR indicado no existe o está deshabilitado.');
        }

        if (!session()->get('isLoggedIn')) {
            $result = $this->processCheckin((int) $qrPoint['user_id'], 'qr', $qrPoint);

            return view('attendance/qr_result', [
                'qrPoint' => $qrPoint,
                'result' => $result,
            ]);
        }

        if (session()->get('role') === 'admin') {
            return redirect()->to('/dashboard')->with('error', 'El check-in QR solo está disponible para empleados.');
        }

        if (!empty($qrPoint['user_id']) && (int) $qrPoint['user_id'] !== (int) session()->get('user_id')) {
            return redirect()->to('/attendance')->with('error', 'Este código QR pertenece a otro empleado.');
        }

        $attendanceModel = new AttendanceModel();
        $activeShift = $attendanceModel->where('user_id', session()->get('user_id'))
            ->where('check_out', null)
            ->orderBy('check_in', 'DESC')
            ->first();

        return view('attendance/qr_confirm', [
            'qrPoint' => $qrPoint,
            'activeShift' => $activeShift,
        ]);
    }

    public function checkin()
    {
        return $this->performCheckin('manual');
    }

    public function checkinQr()
    {
        $pointId = (int) $this->request->getPost('qr_point_id');
        $qrPoint = (new QrPoint())
            ->where('qr_points.id', $pointId)
            ->where('qr_points.is_active', 1)
            ->first();

        if (!$qrPoint) {
            return redirect()->to('/attendance')->with('error', 'El código QR indicado ya no se encuentra disponible.');
        }

        if (!empty($qrPoint['user_id']) && (int) $qrPoint['user_id'] !== (int) session()->get('user_id')) {
            return redirect()->to('/attendance')->with('error', 'No puedes registrar ingreso con un código QR asignado a otro empleado.');
        }

        return $this->performCheckin('qr', $qrPoint);
    }

    public function checkout()
    {
        $model = new AttendanceModel();
        $activeShift = $model->where('user_id', session()->get('user_id'))->where('check_out', null)->first();

        if (!$activeShift) {
            return redirect()->back()->with('error', 'No tienes ningún turno en curso.');
        }

        $model->update($activeShift['id'], ['check_out' => date('Y-m-d H:i:s')]);

        return redirect()->back()->with('success', 'Fichaje de salida cerrado.');
    }

    private function performCheckin(string $method = 'manual', ?array $qrPoint = null)
    {
        $result = $this->processCheckin((int) session()->get('user_id'), $method, $qrPoint);

        return redirect()->to('/attendance')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function processCheckin(int $userId, string $method = 'manual', ?array $qrPoint = null): array
    {
        $model = new AttendanceModel();
        $activeShift = $model->where('user_id', $userId)->where('check_out', null)->first();

        if ($activeShift) {
            return [
                'success' => false,
                'message' => 'Ya existe una jornada abierta para este empleado.',
            ];
        }

        $now = date('Y-m-d H:i:s');
        if ($this->userHasApprovedAbsenceInRange($userId, $now, $now)) {
            return [
                'success' => false,
                'message' => 'No es posible registrar el ingreso porque el empleado tiene una licencia aprobada vigente.',
            ];
        }

        $shift = $this->findTodayShift($userId, $now);
        $shiftId = $shift['id'] ?? null;
        $lateMinutes = null;
        $lateStatus = null;

        if ($shift) {
            $lateMinutes = max(0, (int) floor((strtotime($now) - strtotime($shift['start_time'])) / 60));
            if ($lateMinutes > self::LATE_GRACE_MINUTES) {
                $lateStatus = $lateMinutes <= self::LATE_ALERT_MINUTES ? 'late' : 'late_critical';
                $this->createAnnouncement(
                    'Llegada tardía detectada',
                    sprintf(
                        '%s registró ingreso a las %s para un turno de las %s (%d minutos tarde).',
                        $qrPoint['user_name'] ?? session()->get('name') ?? 'Empleado',
                        date('d/m/Y H:i', strtotime($now)),
                        date('d/m/Y H:i', strtotime($shift['start_time'])),
                        $lateMinutes
                    ),
                    $userId,
                    null,
                    'admin',
                    'lateness',
                    (int) $shift['id']
                );
            }
        }

        $model->insert([
            'user_id' => $userId,
            'shift_id' => $shiftId,
            'check_in' => $now,
            'checkin_method' => $method,
            'qr_point_id' => $qrPoint['id'] ?? null,
            'late_minutes' => $lateMinutes,
            'late_status' => $lateStatus,
        ]);

        return [
            'success' => true,
            'message' => $method === 'qr'
                ? sprintf('Check-in QR registrado correctamente para %s.', $qrPoint['user_name'] ?? 'el empleado')
                : 'Fichaje de entrada registrado correctamente.',
            'check_in' => $now,
        ];
    }

    private function findTodayShift(int $userId, string $now): ?array
    {
        $today = date('Y-m-d', strtotime($now));
        $shiftModel = new Shift();

        return $shiftModel->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->where('DATE(start_time)', $today)
            ->orderBy('start_time', 'ASC')
            ->first();
    }
}
