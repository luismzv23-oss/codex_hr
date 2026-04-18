<?php

namespace App\Models;

use CodeIgniter\Model;

class Department extends Model
{
    protected $table            = 'departments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['parent_id', 'name', 'created_at', 'updated_at'];
}
