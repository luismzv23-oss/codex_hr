<?php

namespace App\Controllers;

use App\Models\QrPoint;
use App\Models\User;

class QrPoints extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $search = trim((string) $this->request->getGet('search'));
        $model = new QrPoint();
        $query = $model->select('qr_points.*, users.name as user_name, users.email as user_email, users.document_id, departments.name as department_name')
            ->join('users', 'users.id = qr_points.user_id', 'left');
        $query->join('departments', 'departments.id = users.department_id', 'left');

        if ($search !== '') {
            $query->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.document_id', $search)
                ->orLike('departments.name', $search)
                ->orLike('qr_points.name', $search)
            ->groupEnd();
        }

        $points = $query->orderBy('users.name', 'ASC')->orderBy('qr_points.name', 'ASC')->findAll();
        $employees = (new User())->where('role !=', 'admin')->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        return view('qr_points/index', [
            'points' => $points,
            'employees' => $employees,
            'search' => $search,
        ]);
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $payload = $this->buildPayload();
        if ($payload === null) {
            return redirect()->back()->withInput();
        }

        $payload['token'] = bin2hex(random_bytes(16));
        $payload['created_at'] = date('Y-m-d H:i:s');

        (new QrPoint())->insert($payload);

        return redirect()->back()->with('success', 'Código QR del empleado creado correctamente.');
    }

    public function update(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $model = new QrPoint();
        $point = $model->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'El código QR indicado no existe.');
        }

        $payload = $this->buildPayload($id);
        if ($payload === null) {
            return redirect()->back()->withInput();
        }

        $payload['updated_at'] = date('Y-m-d H:i:s');
        $model->update($id, $payload);

        return redirect()->back()->with('success', 'Código QR actualizado correctamente.');
    }

    public function toggle(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $model = new QrPoint();
        $point = $model->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'El código QR indicado no existe.');
        }

        $model->update($id, [
            'is_active' => (int) $point['is_active'] === 1 ? 0 : 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', (int) $point['is_active'] === 1 ? 'Código QR deshabilitado.' : 'Código QR habilitado nuevamente.');
    }

    public function regenerate(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $model = new QrPoint();
        $point = $model->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'El código QR indicado no existe.');
        }

        $model->update($id, [
            'token' => bin2hex(random_bytes(16)),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'QR regenerado correctamente. El código anterior deja de funcionar.');
    }

    public function delete(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $model = new QrPoint();
        $point = $model->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'El código QR indicado no existe.');
        }

        $model->delete($id);

        return redirect()->back()->with('success', 'Código QR eliminado correctamente.');
    }

    private function buildPayload(?int $currentId = null): ?array
    {
        $userId = (int) $this->request->getPost('user_id');
        $location = trim((string) $this->request->getPost('location'));
        $description = trim((string) $this->request->getPost('description'));

        $user = (new User())->where('role !=', 'admin')->where('is_active', 1)->find($userId);
        if (!$user) {
            session()->setFlashdata('error', 'Debes seleccionar un empleado válido.');
            return null;
        }

        $existingQuery = (new QrPoint())->where('user_id', $userId);
        if ($currentId !== null) {
            $existingQuery->where('id !=', $currentId);
        }

        if ($existingQuery->countAllResults() > 0) {
            session()->setFlashdata('error', 'El empleado seleccionado ya tiene un código QR asignado.');
            return null;
        }

        return [
            'user_id' => $userId,
            'name' => 'QR de ' . $user['name'],
            'location' => $location !== '' ? $location : null,
            'description' => $description !== '' ? $description : null,
        ];
    }
}
