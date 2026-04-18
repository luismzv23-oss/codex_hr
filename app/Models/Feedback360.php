<?php
namespace App\Models;
use CodeIgniter\Model;

class Feedback360 extends Model {
    protected $table = 'feedback_360';
    protected $primaryKey = 'id';
    protected $allowedFields = ['evaluation_id', 'evaluatee_id', 'evaluator_id', 'rater_type', 'score', 'comment', 'created_at', 'updated_at'];
}
