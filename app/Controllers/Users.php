<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\User;

class Users extends BaseController
{
    public function index()
    {
        if (session()->get('role') != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.*, departments.name as department_name');
        $builder->join('departments', 'departments.id = users.department_id', 'left');
        $builder->orderBy('role', 'ASC')->orderBy('users.name', 'ASC');

        $deptModel = new Department();

        return view('users/index', [
            'users' => $builder->get()->getResultArray(),
            'departments' => $deptModel->findAll(),
        ]);
    }

    public function store()
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $userModel = new User();
        $documentId = trim((string) $this->request->getPost('document_id'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $hireDate = $this->request->getPost('hire_date');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'El correo electrónico no es válido.');
        }

        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', 'El correo electrónico ya está registrado.');
        }

        if ($userModel->where('document_id', $documentId)->first()) {
            return redirect()->back()->withInput()->with('error', 'El documento ya esta registrado.');
        }

        if (strlen($password) < 8) {
            return redirect()->back()->withInput()->with('error', 'La contraseña debe tener al menos 8 caracteres.');
        }

        if (!empty($hireDate) && !strtotime((string) $hireDate)) {
            return redirect()->back()->withInput()->with('error', 'La fecha de incorporación no es válida.');
        }

        $userModel->insert([
            'name' => $this->request->getPost('name'),
            'document_id' => $documentId,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'employee_type' => $this->request->getPost('employee_type') ?: null,
            'salary_base' => $this->request->getPost('salary_base') ?: null,
            'hire_date' => $hireDate ?: null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Usuario registrado correctamente.');
    }

    public function toggleStatus($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'No puedes modificar tu propio estado aquí.');
        }

        $userModel = new User();
        $user = $userModel->find($id);
        if ($user) {
            $userModel->update($id, ['is_active' => $user['is_active'] ? 0 : 1]);
        }

        return redirect()->back()->with('success', 'El estado del empleado ha sido actualizado.');
    }

    public function update($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $userModel = new User();
        $documentId = trim((string) $this->request->getPost('document_id'));
        $existingDocument = $userModel->where('document_id', $documentId)->where('id !=', $id)->first();
        if ($existingDocument) {
            return redirect()->back()->with('error', 'Ya existe otro empleado con ese documento.');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'document_id' => $documentId,
            'role' => $this->request->getPost('role'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'employee_type' => $this->request->getPost('employee_type') ?: null,
            'salary_base' => $this->request->getPost('salary_base') ?: null,
            'hire_date' => $this->request->getPost('hire_date') ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($this->request->getPost('password'))) {
            if (strlen((string) $this->request->getPost('password')) < 8) {
                return redirect()->back()->with('error', 'La nueva contraseña debe tener al menos 8 caracteres.');
            }

            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);

        return redirect()->back()->with('success', 'Empleado actualizado con éxito.');
    }

    public function delete($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $userModel = new User();
        $db = \Config\Database::connect();

        $db->transStart();
        $db->table('shifts')->where('user_id', $id)->set(['user_id' => null])->update();
        $db->table('attendance')->where('user_id', $id)->delete();
        $db->table('absences')->where('user_id', $id)->delete();
        $db->table('employee_objectives')->where('user_id', $id)->delete();
        $db->table('employee_evaluations')->where('user_id', $id)->delete();
        $db->table('feedback_360')->where('evaluatee_id', $id)->delete();
        $db->table('feedback_360')->where('evaluator_id', $id)->delete();
        $userModel->delete($id);
        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'No se pudo eliminar el empleado. La operacion fue revertida.');
        }

        return redirect()->back()->with('success', 'Cuenta de empleado y registros eliminados permanentemente.');
    }
}
