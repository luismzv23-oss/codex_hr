<?php

namespace App\Controllers;

use App\Models\QrPoint;

class QrPoints extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $points = (new QrPoint())->orderBy('name', 'ASC')->findAll();

        return view('qr_points/index', ['points' => $points]);
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

        return redirect()->back()->with('success', 'Punto QR creado correctamente.');
    }

    public function update(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $model = new QrPoint();
        $point = $model->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'El punto QR indicado no existe.');
        }

        $payload = $this->buildPayload();
        if ($payload === null) {
            return redirect()->back()->withInput();
        }

        $payload['updated_at'] = date('Y-m-d H:i:s');
        $model->update($id, $payload);

        return redirect()->back()->with('success', 'Punto QR actualizado correctamente.');
    }

    public function toggle(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $model = new QrPoint();
        $point = $model->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'El punto QR indicado no existe.');
        }

        $model->update($id, [
            'is_active' => (int) $point['is_active'] === 1 ? 0 : 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', (int) $point['is_active'] === 1 ? 'Punto QR deshabilitado.' : 'Punto QR habilitado nuevamente.');
    }

    public function regenerate(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $model = new QrPoint();
        $point = $model->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'El punto QR indicado no existe.');
        }

        $model->update($id, [
            'token' => bin2hex(random_bytes(16)),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'QR regenerado correctamente. El cÃ³digo anterior deja de funcionar.');
    }

    private function buildPayload(): ?array
    {
        $name = trim((string) $this->request->getPost('name'));
        $location = trim((string) $this->request->getPost('location'));
        $description = trim((string) $this->request->getPost('description'));

        if ($name === '') {
            session()->setFlashdata('error', 'Debes indicar el nombre del punto QR.');
            return null;
        }

        return [
            'name' => $name,
            'location' => $location !== '' ? $location : null,
            'description' => $description !== '' ? $description : null,
        ];
    }
}
