<?php

namespace App\Controllers;

use App\Models\Schedule;

class Schedules extends BaseController
{
    public function index()
    {
        if (session()->get('role') != 'admin') return redirect()->to('/dashboard');
        
        $model = new Schedule();
        $schedules = $model->findAll();
        
        return view('schedules/index', ['schedules' => $schedules]);
    }

    public function store()
    {
        if (session()->get('role') != 'admin') return redirect()->back();
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        if (empty($startTime) || empty($endTime) || $endTime <= $startTime) {
            return redirect()->back()->withInput()->with('error', 'La hora de fin debe ser posterior a la hora de inicio.');
        }
        
        $model = new Schedule();
        $model->insert([
            'name' => $this->request->getPost('name'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'color' => $this->request->getPost('color'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        return redirect()->back()->with('success', 'Modelo de horario creado.');
    }

    public function delete($id)
    {
        if (session()->get('role') != 'admin') return redirect()->back();
        
        $model = new Schedule();
        $model->delete($id);
        
        return redirect()->back()->with('success', 'Horario eliminado.');
    }

    public function update($id)
    {
        if (session()->get('role') != 'admin') return redirect()->back();
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        if (empty($startTime) || empty($endTime) || $endTime <= $startTime) {
            return redirect()->back()->with('error', 'La hora de fin debe ser posterior a la hora de inicio.');
        }
        
        $model = new Schedule();
        $model->update($id, [
            'name' => $this->request->getPost('name'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'color' => $this->request->getPost('color'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        return redirect()->back()->with('success', 'Horario base actualizado.');
    }
}
