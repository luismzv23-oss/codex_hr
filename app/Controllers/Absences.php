<?php

namespace App\Controllers;

use App\Models\Absence;

class Absences extends BaseController
{
    private const ALLOWED_ATTACHMENT_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png'];
    private const ALLOWED_ATTACHMENT_MIME_TYPES = ['application/pdf', 'image/jpeg', 'image/png'];
    private const MAX_ATTACHMENT_SIZE_KB = 5120;

    public function index()
    {
        $absModel = new Absence();
        if (session()->get('role') == 'admin') {
            $data = $absModel->select('absences.*, users.name')
                ->join('users', 'users.id = absences.user_id')
                ->orderBy('created_at', 'DESC')
                ->findAll();

            return view('absences/admin', ['absences' => $data]);
        }

        $data = $absModel->where('user_id', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('absences/employee', ['absences' => $data]);
    }

    public function store()
    {
        $model = new Absence();
        $file = $this->request->getFile('attachment');
        $filePath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $extension = strtolower((string) $file->guessExtension());
            $mimeType = $file->getMimeType();
            $sizeKb = $file->getSizeByUnit('kb');

            if (!in_array($extension, self::ALLOWED_ATTACHMENT_EXTENSIONS, true)
                || !in_array($mimeType, self::ALLOWED_ATTACHMENT_MIME_TYPES, true)) {
                return redirect()->back()->withInput()->with('error', 'El adjunto debe ser PDF, JPG o PNG.');
            }

            if ($sizeKb > self::MAX_ATTACHMENT_SIZE_KB) {
                return redirect()->back()->withInput()->with('error', 'El adjunto supera el máximo permitido de 5 MB.');
            }

            $uploadPath = WRITEPATH . 'uploads/absences';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $filePath = 'absences/' . $newName;
        }

        $model->insert([
            'user_id' => session()->get('user_id'),
            'type' => $this->request->getPost('type'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'status' => 'Pendiente',
            'reason' => $this->request->getPost('reason'),
            'attachment' => $filePath,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Solicitud enviada correctamente. En espera de aprobacion.');
    }

    public function attachment($id)
    {
        [$absence, $fullPath] = $this->resolveAttachment($id);

        return $this->response->download($fullPath, null)->setFileName(basename($fullPath));
    }

    public function preview($id)
    {
        [, $fullPath] = $this->resolveAttachment($id);

        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"')
            ->setBody((string) file_get_contents($fullPath));
    }

    public function updateStatus($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $status = $this->request->getPost('status');
        if (!in_array($status, ['Pendiente', 'Aprobado', 'Rechazado'], true)) {
            return redirect()->back()->with('error', 'El estado indicado no es valido.');
        }

        $model = new Absence();
        $model->update($id, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'El estado de la licencia ha sido actualizado.');
    }

    private function resolveAttachment($id): array
    {
        $model = new Absence();
        $absence = $model->find($id);

        if (!$absence || empty($absence['attachment'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $isAdmin = session()->get('role') === 'admin';
        if (!$isAdmin && (int) $absence['user_id'] !== (int) session()->get('user_id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $fullPath = WRITEPATH . 'uploads/' . $absence['attachment'];
        if (!is_file($fullPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return [$absence, $fullPath];
    }
}
