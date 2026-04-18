<?php
namespace App\Models;
use CodeIgniter\Model;

class Objective extends Model {
    protected $table = 'objectives';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'type', 'weight', 'start_date', 'end_date', 'created_at', 'updated_at'];
}
