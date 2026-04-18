<?php
namespace App\Models;
use CodeIgniter\Model;

class EmployeeEvaluation extends Model {
    protected $table = 'employee_evaluations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['evaluation_id', 'user_id', 'total_score', 'comments', 'created_at', 'updated_at'];
}
