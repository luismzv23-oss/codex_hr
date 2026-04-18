<?php
namespace App\Controllers;
use App\Models\Objective;
use App\Models\User;
use App\Models\EmployeeObjective;

class Objectives extends BaseController {
    public function index() {
        if (session()->get('role') != 'admin') return redirect()->to('/dashboard');
        
        $objModel = new Objective();
        $userModel = new User();
        
        $objectives = $objModel->findAll();
        $users = $userModel->where('role !=', 'admin')->findAll();
        
        $db = \Config\Database::connect();
        $assignments = $db->table('employee_objectives')
                          ->select('employee_objectives.*, objectives.title, objectives.weight, users.name')
                          ->join('objectives', 'objectives.id = employee_objectives.objective_id')
                          ->join('users', 'users.id = employee_objectives.user_id')
                          ->get()->getResultArray();
                          
        return view('objectives/index', [
            'objectives' => $objectives, 
            'users' => $users,
            'assignments' => $assignments
        ]);
    }
    
    public function store() {
        if (session()->get('role') != 'admin') return redirect()->back();
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $weight = (float) $this->request->getPost('weight');
        if (empty($startDate) || empty($endDate) || $endDate < $startDate) {
            return redirect()->back()->withInput()->with('error', 'Las fechas del objetivo no son validas.');
        }
        if ($weight <= 0 || $weight > 100) {
            return redirect()->back()->withInput()->with('error', 'El peso del objetivo debe estar entre 0 y 100.');
        }
        $model = new Objective();
        $model->insert([
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'weight' => $weight,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->back()->with('success', 'Meta organizacional registrada.');
    }
    
    public function assign() {
        if (session()->get('role') != 'admin') return redirect()->back();
        $model = new EmployeeObjective();
        $userIds = $this->request->getPost('user_ids');
        $objId = $this->request->getPost('objective_id');
        
        if (empty($userIds) || empty($objId)) return redirect()->back()->with('error', 'Revisa los campos de asignación.');

        foreach($userIds as $uid) {
            if (!$model->where('user_id', $uid)->where('objective_id', $objId)->first()) {
                $model->insert([
                    'user_id' => $uid,
                    'objective_id' => $objId,
                    'progress' => 0,
                    'status' => 'En curso',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        return redirect()->back()->with('success', 'Objetivo delegado al personal seleccionado.');
    }
    
    public function update_progress($id) {
        $model = new EmployeeObjective();
        $eo = $model->find($id);
        if ($eo && $eo['user_id'] == session()->get('user_id')) {
            $progress = max(0, min(100, (int) $this->request->getPost('progress')));
            $status = $progress >= 100 ? 'Cumplido' : 'En curso';
            $model->update($id, ['progress' => $progress, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
            return redirect()->back()->with('success', 'Progreso personal actualizado al '.$progress.'%.');
        }
        return redirect()->back();
    }
}
