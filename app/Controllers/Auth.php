<?php

namespace App\Controllers;

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

        return view('auth/login');
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
