<?php
namespace App\Models;
use CodeIgniter\Model;

class PerformanceEvaluation extends Model {
    protected $table = 'performance_evaluations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'start_date', 'end_date', 'status', 'created_at', 'updated_at'];
}
