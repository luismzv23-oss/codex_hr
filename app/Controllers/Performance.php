<?php

namespace App\Controllers;

use App\Models\EmployeeEvaluation;
use App\Models\Feedback360;
use App\Models\PerformanceEvaluation;
use App\Models\User;

class Performance extends BaseController
{
    private const ALLOWED_RATER_TYPES = ['Autoevaluacion', 'Companero', 'Liderazgo'];

    public function index()
    {
        $perfModel = new PerformanceEvaluation();
        $evaluations = $perfModel->orderBy('id', 'DESC')->findAll();

        $db = \Config\Database::connect();

        if (session()->get('role') == 'admin') {
            $results = $db->table('employee_evaluations')
                ->select('employee_evaluations.*, performance_evaluations.name as eval_name, users.name as user_name')
                ->join('performance_evaluations', 'performance_evaluations.id = employee_evaluations.evaluation_id')
                ->join('users', 'users.id = employee_evaluations.user_id')
                ->get()->getResultArray();

            return view('performance/admin', ['evaluations' => $evaluations, 'results' => $results]);
        }

        $uid = session()->get('user_id');
        $objectives = $db->table('employee_objectives')
            ->select('employee_objectives.*, objectives.title, objectives.description, objectives.end_date')
            ->join('objectives', 'objectives.id = employee_objectives.objective_id')
            ->where('employee_objectives.user_id', $uid)
            ->get()->getResultArray();

        $users = clone (new User());
        $peers = $users->where('role !=', 'admin')->where('id !=', $uid)->findAll();

        return view('performance/employee', [
            'evaluations' => $evaluations,
            'objectives' => $objectives,
            'users' => $peers,
        ]);
    }

    public function create_campaign()
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        if (empty($startDate) || empty($endDate) || $endDate < $startDate) {
            return redirect()->back()->withInput()->with('error', 'Las fechas de la campaña no son válidas.');
        }

        $model = new PerformanceEvaluation();
        $model->insert([
            'name' => $this->request->getPost('name'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Abierta',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Período de evaluación inaugurado.');
    }

    public function submit_feedback()
    {
        $evaluationId = (int) $this->request->getPost('evaluation_id');
        $evaluateeId = (int) $this->request->getPost('evaluatee_id');
        $evaluatorId = (int) session()->get('user_id');
        $raterType = (string) $this->request->getPost('rater_type');
        $score = (float) $this->request->getPost('score');
        $comment = trim((string) $this->request->getPost('comment'));

        $campaign = (new PerformanceEvaluation())->find($evaluationId);
        if (!$campaign || $campaign['status'] !== 'Abierta') {
            return redirect()->back()->withInput()->with('error', 'La campaña seleccionada no está disponible para recibir feedback.');
        }

        $userModel = new User();
        $evaluatee = $userModel->find($evaluateeId);
        if (!$evaluatee || $evaluatee['role'] === 'admin') {
            return redirect()->back()->withInput()->with('error', 'El destinatario del feedback no es valido.');
        }

        if (!in_array($raterType, self::ALLOWED_RATER_TYPES, true)) {
            return redirect()->back()->withInput()->with('error', 'El tipo de evaluación no es válido.');
        }

        if ($score < 1 || $score > 5) {
            return redirect()->back()->withInput()->with('error', 'La puntuación debe estar entre 1 y 5.');
        }

        if ($comment === '') {
            return redirect()->back()->withInput()->with('error', 'Debes incluir un comentario para registrar el feedback.');
        }

        if ($raterType === 'Autoevaluacion' && $evaluateeId !== $evaluatorId) {
            return redirect()->back()->withInput()->with('error', 'La autoevaluación solo puede dirigirse a tu propio perfil.');
        }

        if ($raterType !== 'Autoevaluacion' && $evaluateeId === $evaluatorId) {
            return redirect()->back()->withInput()->with('error', 'Ese tipo de feedback no puede dirigirse a tu propio perfil.');
        }

        $fbModel = new Feedback360();
        $existingFeedback = $fbModel->where('evaluation_id', $evaluationId)
            ->where('evaluatee_id', $evaluateeId)
            ->where('evaluator_id', $evaluatorId)
            ->where('rater_type', $raterType)
            ->first();

        if ($existingFeedback) {
            return redirect()->back()->withInput()->with('error', 'Ya registraste un feedback de este tipo para esta campaña.');
        }

        $fbModel->insert([
            'evaluation_id' => $evaluationId,
            'evaluatee_id' => $evaluateeId,
            'evaluator_id' => $evaluatorId,
            'rater_type' => $raterType,
            'score' => $score,
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Feedback 360 cargado exitosamente.');
    }

    public function close_calculation($id)
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back();
        }

        $perfModel = new PerformanceEvaluation();
        $campaign = $perfModel->find($id);
        if (!$campaign) {
            return redirect()->back()->with('error', 'La campaña seleccionada no existe.');
        }

        if ($campaign['status'] !== 'Abierta') {
            return redirect()->back()->with('error', 'La campaña ya fue cerrada anteriormente.');
        }

        $db = \Config\Database::connect();
        $users = (new User())->where('role !=', 'admin')->findAll();
        $eeModel = new EmployeeEvaluation();

        if ($eeModel->where('evaluation_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'La campaña ya tiene resultados calculados. No se generarán duplicados.');
        }

        $db->transStart();
        $perfModel->update($id, ['status' => 'Cerrada', 'updated_at' => date('Y-m-d H:i:s')]);

        foreach ($users as $u) {
            $uid = $u['id'];

            $objs = $db->table('employee_objectives')
                ->select('employee_objectives.progress, objectives.weight')
                ->join('objectives', 'objectives.id = employee_objectives.objective_id')
                ->where('employee_objectives.user_id', $uid)
                ->get()->getResultArray();

            $objScore = 0;
            if (count($objs) > 0) {
                $sum = 0;
                $totalWeight = 0;
                foreach ($objs as $o) {
                    $sum += ($o['progress'] * $o['weight']);
                    $totalWeight += $o['weight'];
                }

                if ($totalWeight > 0) {
                    $objScore = $sum / $totalWeight;
                }
            }

            $feedbacks = $db->table('feedback_360')
                ->where('evaluatee_id', $uid)
                ->where('evaluation_id', $id)
                ->get()->getResultArray();

            $fbScore = 0;
            if (count($feedbacks) > 0) {
                $sumTb = 0;
                foreach ($feedbacks as $fb) {
                    $sumTb += ($fb['score'] * 20);
                }

                $fbScore = $sumTb / count($feedbacks);
            }

            $finalScore = ($objScore * 0.5) + ($fbScore * 0.5);

            if (count($objs) > 0 || count($feedbacks) > 0) {
                $eeModel->insert([
                    'evaluation_id' => $id,
                    'user_id' => $uid,
                    'total_score' => $finalScore,
                    'comments' => 'Cálculo algorítmico generado (50% KPI objetivos / 50% feedback 360).',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'No se pudo cerrar la campaña. La transacción fue revertida.');
        }

        return redirect()->back()->with('success', 'Período finalizado y motor de cálculo ejecutado.');
    }
}
