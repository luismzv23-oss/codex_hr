<?php
namespace App\Models;
use CodeIgniter\Model;

class EmployeeObjective extends Model {
    protected $table = 'employee_objectives';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'objective_id', 'progress', 'status', 'created_at', 'updated_at'];
}
