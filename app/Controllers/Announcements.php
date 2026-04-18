<?php

namespace App\Controllers;

use App\Models\Announcement as AnnouncementModel;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;

class Announcements extends BaseController
{
    public function index()
    {
        if (session()->get('role') === 'admin') {
            $this->syncAbsenceAlerts();
        }

        $model = new AnnouncementModel();
        $currentUserId = (int) session()->get('user_id');
        $currentRole = (string) session()->get('role');
        $filters = [
            'date_from' => trim((string) $this->request->getGet('date_from')),
            'date_to' => trim((string) $this->request->getGet('date_to')),
            'category' => trim((string) $this->request->getGet('category')),
            'recipient' => trim((string) $this->request->getGet('recipient')),
        ];

        $query = $model->select('announcements.*, authors.name as author_name, recipients.name as recipient_name, shifts.start_time as shift_start_time, shifts.end_time as shift_end_time, schedules.name as schedule_name, shift_users.name as shift_user_name')
            ->join('users as authors', 'authors.id = announcements.created_by', 'left')
            ->join('users as recipients', 'recipients.id = announcements.recipient_user_id', 'left')
            ->join('shifts', 'shifts.id = announcements.related_shift_id', 'left')
            ->join('schedules', 'schedules.id = shifts.schedule_id', 'left')
            ->join('users as shift_users', 'shift_users.id = shifts.user_id', 'left');

        if ($currentRole !== 'admin') {
            $query->groupStart()
                ->groupStart()
                    ->where('announcements.recipient_user_id', null)
                    ->where('announcements.recipient_role', null)
                ->groupEnd()
                ->orWhere('announcements.recipient_user_id', $currentUserId)
                ->orWhere('announcements.recipient_role', $currentRole)
            ->groupEnd();
        }

        if ($filters['date_from'] !== '') {
            $query->where('DATE(announcements.created_at) >=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $query->where('DATE(announcements.created_at) <=', $filters['date_to']);
        }

        if ($filters['category'] !== '') {
            $query->where('announcements.category', $filters['category']);
        }

        if ($currentRole === 'admin' && $filters['recipient'] !== '') {
            if ($filters['recipient'] === 'all') {
                $query->where('announcements.recipient_user_id', null)
                    ->where('announcements.recipient_role', null);
            } elseif ($filters['recipient'] === 'admins') {
                $query->where('announcements.recipient_role', 'admin');
            } elseif (ctype_digit($filters['recipient'])) {
                $query->where('announcements.recipient_user_id', (int) $filters['recipient']);
            }
        }

        $users = $currentRole === 'admin'
            ? (new User())->where('role !=', 'admin')->orderBy('name', 'ASC')->findAll()
            : [];

        return view('announcements/index', [
            'announcements' => $query->orderBy('announcements.created_at', 'DESC')->findAll(),
            'filters' => $filters,
            'users' => $users,
            'categories' => [
                'general' => 'General',
                'shift_assignment' => 'Asignación de turno',
                'lateness' => 'Llegada tardía',
                'absence_notice' => 'Inasistencia',
            ],
        ]);
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        $title = trim((string) $this->request->getPost('title'));
        $content = trim((string) $this->request->getPost('content'));
        $targetType = (string) $this->request->getPost('target_type');
        $recipientUserId = null;

        if ($title === '' || $content === '') {
            return redirect()->back()->withInput()->with('error', 'Debes completar el título y el contenido del aviso.');
        }

        if ($targetType === 'specific') {
            $recipientUserId = (int) $this->request->getPost('recipient_user_id');
            $user = (new User())->where('role !=', 'admin')->find($recipientUserId);
            if (!$user) {
                return redirect()->back()->withInput()->with('error', 'Debes seleccionar un empleado válido para el aviso.');
            }
        }

        $model = new AnnouncementModel();
        $model->insert([
            'title' => $title,
            'content' => $content,
            'category' => 'general',
            'created_by' => (int) session()->get('user_id'),
            'recipient_user_id' => $targetType === 'specific' ? $recipientUserId : null,
            'recipient_role' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Aviso publicado correctamente.');
    }

    private function syncAbsenceAlerts(): void
    {
        $shiftModel = new Shift();
        $attendanceModel = new Attendance();
        $announcementModel = new AnnouncementModel();

        $shifts = $shiftModel->select('shifts.*, users.name as user_name, schedules.name as schedule_name')
            ->join('users', 'users.id = shifts.user_id')
            ->join('schedules', 'schedules.id = shifts.schedule_id', 'left')
            ->where('shifts.user_id IS NOT NULL')
            ->where('shifts.is_active', 1)
            ->where('shifts.status', 'approved')
            ->where('shifts.end_time <', date('Y-m-d H:i:s'))
            ->orderBy('shifts.start_time', 'DESC')
            ->findAll();

        foreach ($shifts as $shift) {
            $alreadyNotified = $announcementModel->where('category', 'absence_notice')
                ->where('related_shift_id', (int) $shift['id'])
                ->countAllResults() > 0;

            if ($alreadyNotified) {
                continue;
            }

            $hasAttendance = $attendanceModel->where('shift_id', (int) $shift['id'])->countAllResults() > 0;
            if ($hasAttendance) {
                continue;
            }

            if ($this->userHasApprovedAbsenceInRange((int) $shift['user_id'], $shift['start_time'], $shift['end_time'])) {
                continue;
            }

            $this->createAnnouncement(
                'Inasistencia detectada',
                sprintf(
                    '%s no registró asistencia para el turno "%s" programado del %s al %s.',
                    $shift['user_name'],
                    $shift['schedule_name'] ?: 'Turno libre',
                    date('d/m/Y H:i', strtotime($shift['start_time'])),
                    date('d/m/Y H:i', strtotime($shift['end_time']))
                ),
                (int) session()->get('user_id'),
                null,
                'admin',
                'absence_notice',
                (int) $shift['id']
            );
        }
    }
}
