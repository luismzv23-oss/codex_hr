<?php

namespace App\Models;

use CodeIgniter\Model;

class Schedule extends Model
{
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'start_time', 'end_time', 'color', 'created_at', 'updated_at'];
}
