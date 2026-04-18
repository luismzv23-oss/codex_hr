<?php

namespace App\Controllers;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\User;

class Shifts extends BaseController
{
    public function index()
    {
        $shiftModel = new Shift();
        $myShifts = $shiftModel->select('shifts.*, schedules.name as schedule_name, schedules.color as schedule_color')
            ->join('schedules', 'schedules.id = shifts.schedule_id', 'left')
            ->where('user_id', session()->get('user_id'))
            ->where('is_active', 1)
            ->orderBy('shifts.start_time', 'ASC')
            ->findAll();

        return view('shifts/index', ['shifts' => $myShifts]);
    }

    public function admin()
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        $shiftModel = new Shift();
        $userModel = new User();
        $scheduleModel = new Schedule();

        $shifts = $shiftModel->select('shifts.*, users.name, schedules.name as schedule_name')
            ->join('users', 'users.id = shifts.user_id', 'left')
            ->join('schedules', 'schedules.id = shifts.schedule_id', 'left')
            ->orderBy('shifts.start_time', 'DESC')
            ->findAll();

        $users = $userModel->where('role !=', 'admin')->where('is_active', 1)->findAll();
        $schedules = $scheduleModel->orderBy('name', 'ASC')->findAll();

        return view('shifts/admin', [
            'shifts' => $shifts,
            'users' => $users,
            'schedules' => $schedules,
            'minAssignDate' => date('Y-m-d', strtotime('+2 days')),
        ]);
    }

    public function store()
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        [$userId, $scheduleId, $startTime, $endTime] = $this->buildShiftPayloadFromRequest();
        if ($startTime === null || $endTime === null) {
            return redirect()->back()->withInput();
        }

        if (!empty($userId) && $this->userHasApprovedAbsenceInRange((int) $userId, $startTime, $endTime)) {
            return redirect()->back()->withInput()->with('error', 'No se puede asignar un turno a un empleado con licencia aprobada en esas fechas.');
        }

        $shiftModel = new Shift();
        $existingShift = $shiftModel->where('user_id', empty($userId) ? null : $userId)
            ->where('start_time', $startTime)
            ->where('end_time', $endTime)
            ->first();

        if ($existingShift) {
            return redirect()->back()->withInput()->with('error', 'Ya existe un turno idéntico para esa fecha y ese empleado.');
        }

        $shiftModel->insert([
            'user_id' => empty($userId) ? null : $userId,
            'schedule_id' => $scheduleId ?: null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($userId)) {
            $this->notifyShiftAssignment((int) $userId, $startTime, $scheduleId);
        }

        return redirect()->back()->with('success', 'Turno registrado correctamente.');
    }

    public function update($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $shiftModel = new Shift();
        $shift = $shiftModel->find($id);
        if (!$shift) {
            return redirect()->back()->with('error', 'El turno indicado no existe.');
        }

        [$userId, $scheduleId, $startTime, $endTime] = $this->buildShiftPayloadFromRequest();
        if ($startTime === null || $endTime === null) {
            return redirect()->back()->withInput();
        }

        if (!empty($userId) && $this->userHasApprovedAbsenceInRange((int) $userId, $startTime, $endTime)) {
            return redirect()->back()->withInput()->with('error', 'No se puede asignar un turno a un empleado con licencia aprobada en esas fechas.');
        }

        $shiftModel->update($id, [
            'user_id' => empty($userId) ? null : $userId,
            'schedule_id' => $scheduleId ?: null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $shift['status'] === 'disabled' ? 'disabled' : 'pending',
            'approved_at' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($userId)) {
            $this->notifyShiftAssignment((int) $userId, $startTime, $scheduleId);
        }

        return redirect()->back()->with('success', 'Turno actualizado correctamente.');
    }

    public function toggle($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $shiftModel = new Shift();
        $shift = $shiftModel->find($id);
        if (!$shift) {
            return redirect()->back()->with('error', 'El turno indicado no existe.');
        }

        $isActive = (int) $shift['is_active'] === 1 ? 0 : 1;
        $newStatus = $isActive ? 'pending' : 'disabled';
        $shiftModel->update($id, [
            'is_active' => $isActive,
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', $isActive ? 'Turno habilitado nuevamente.' : 'Turno deshabilitado correctamente.');
    }

    public function approve($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $shiftModel = new Shift();
        $shift = $shiftModel->find($id);
        if (!$shift) {
            return redirect()->back()->with('error', 'El turno indicado no existe.');
        }

        if ((int) $shift['is_active'] !== 1) {
            return redirect()->back()->with('error', 'No puedes aprobar un turno deshabilitado.');
        }

        if (!empty($shift['user_id']) && $this->userHasApprovedAbsenceInRange((int) $shift['user_id'], $shift['start_time'], $shift['end_time'])) {
            return redirect()->back()->with('error', 'No se puede aprobar este turno porque el empleado tiene licencia aprobada en esas fechas.');
        }

        $shiftModel->update($id, [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Turno aprobado correctamente.');
    }

    private function buildShiftPayloadFromRequest(): array
    {
        $userId = $this->request->getPost('user_id');
        $scheduleId = (int) $this->request->getPost('schedule_id');
        $shiftDate = $this->request->getPost('shift_date');
        $manualStart = $this->request->getPost('manual_start_time');
        $manualEnd = $this->request->getPost('manual_end_time');

        if (empty($shiftDate)) {
            session()->setFlashdata('error', 'Debes indicar la fecha del turno.');
            return [$userId, null, null, null];
        }

        $minAllowedDate = date('Y-m-d', strtotime('+2 days'));
        if ($shiftDate < $minAllowedDate) {
            session()->setFlashdata('error', 'Los turnos solo pueden programarse con al menos 2 días de anticipación.');
            return [$userId, null, null, null];
        }

        $startTime = null;
        $endTime = null;

        if ($scheduleId > 0) {
            $schedule = (new Schedule())->find($scheduleId);
            if (!$schedule) {
                session()->setFlashdata('error', 'Debes seleccionar un horario base válido.');
                return [$userId, null, null, null];
            }

            $startTime = $shiftDate . ' ' . $schedule['start_time'] . ':00';
            $endTime = $shiftDate . ' ' . $schedule['end_time'] . ':00';
            if ($schedule['end_time'] <= $schedule['start_time']) {
                $endTime = date('Y-m-d H:i:s', strtotime($endTime . ' +1 day'));
            }
        } else {
            if (empty($manualStart) || empty($manualEnd)) {
                session()->setFlashdata('error', 'Para asignación libre debes informar hora de inicio y fin.');
                return [$userId, null, null, null];
            }

            $startTime = $shiftDate . ' ' . $manualStart . ':00';
            $endTime = $shiftDate . ' ' . $manualEnd . ':00';
            if ($manualEnd <= $manualStart) {
                $endTime = date('Y-m-d H:i:s', strtotime($endTime . ' +1 day'));
            }
        }

        return [$userId, $scheduleId, $startTime, $endTime];
    }

    private function notifyShiftAssignment(int $userId, string $startTime, ?int $scheduleId): void
    {
        $scheduleName = 'Turno libre';
        if (!empty($scheduleId)) {
            $schedule = (new Schedule())->find($scheduleId);
            if ($schedule) {
                $scheduleName = $schedule['name'];
            }
        }

        $this->createAnnouncement(
            'Nuevo turno asignado',
            sprintf('Se te asignó el turno "%s" para el %s.', $scheduleName, date('d/m/Y H:i', strtotime($startTime))),
            (int) session()->get('user_id'),
            $userId,
            null,
            'shift_assignment'
        );
    }
}
