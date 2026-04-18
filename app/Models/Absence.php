<?php

namespace App\Models;

use CodeIgniter\Model;

class Absence extends Model
{
    protected $table            = 'absences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'type', 'start_date', 'end_date', 'status', 'reason', 'attachment', 'created_at', 'updated_at'];
}
