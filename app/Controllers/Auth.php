<?php

namespace App\Controllers;

use App\Models\QrPoint;
use App\Models\User;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod(true) === 'POST') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $userModel = new User();
            $user = $userModel->where('email', $email)->first();

            if ($user && password_verify($password, $user['password'])) {
                if ((int)$user['is_active'] === 0) {
                    return redirect()->back()->with('error', 'Tu cuenta ha sido suspendida. Contacta al administrador.');
                }
                session()->set([
                    'isLoggedIn' => true,
                    'user_id' => $user['id'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                ]);

                $intendedUrl = session()->get('intended_url');
                if (!empty($intendedUrl)) {
                    session()->remove('intended_url');
                    return redirect()->to($intendedUrl)->with('success', 'Bienvenido ' . $user['name']);
                }

                return redirect()->to('/dashboard')->with('success', 'Bienvenido ' . $user['name']);
            }

            return redirect()->back()->with('error', 'Credenciales incorrectas');
        }

        $documentId = trim((string) $this->request->getGet('document_id'));
        $publicQr = null;
        $publicQrError = null;

        if ($documentId !== '') {
            $publicQr = (new QrPoint())
                ->select('qr_points.*, users.name as user_name, users.email as user_email, users.document_id, departments.name as department_name')
                ->join('users', 'users.id = qr_points.user_id')
                ->join('departments', 'departments.id = users.department_id', 'left')
                ->where('users.document_id', $documentId)
                ->where('users.is_active', 1)
                ->where('qr_points.is_active', 1)
                ->first();

            if (!$publicQr) {
                $publicQrError = 'No se encontró un QR activo para el documento indicado.';
            }
        }

        return view('auth/login', [
            'documentId' => $documentId,
            'publicQr' => $publicQr,
            'publicQrError' => $publicQrError,
        ]);
    }

    public function logout()
    {
        if ($this->request->getMethod(true) !== 'POST') {
            return redirect()->to('/dashboard');
        }

        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
