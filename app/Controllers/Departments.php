<?php

namespace App\Controllers;

use App\Models\Department;

class Departments extends BaseController
{
    public function index()
    {
        if (session()->get('role') != 'admin') return redirect()->to('/dashboard');
        $model = new Department();
        return view('departments/index', ['departments' => $model->findAll()]);
    }

    public function store()
    {
        if (session()->get('role') != 'admin') return redirect()->back();
        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'El nombre del departamento es obligatorio.');
        }
        $model = new Department();
        $model->insert([
            'name' => $name,
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return redirect()->back()->with('success', 'Departamento creado exitosamente.');
    }

    public function update($id)
    {
        if (session()->get('role') != 'admin') return redirect()->back();
        if ((int) $this->request->getPost('parent_id') === (int) $id) {
            return redirect()->back()->with('error', 'Un departamento no puede depender de sí mismo.');
        }
        $model = new Department();
        $model->update($id, [
            'name' => $this->request->getPost('name'),
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return redirect()->back()->with('success', 'Departamento actualizado.');
    }

    public function delete($id)
    {
        if (session()->get('role') != 'admin') return redirect()->back();
        $model = new Department();
        if (!$model->find($id)) {
            return redirect()->back()->with('error', 'El departamento seleccionado no existe.');
        }
        $model->where('parent_id', $id)->set(['parent_id' => null])->update();
        
        $db = \Config\Database::connect();
        $db->table('users')->where('department_id', $id)->update(['department_id' => null]);
        
        $model->delete($id);
        return redirect()->back()->with('success', 'Departamento eliminado.');
    }
}
