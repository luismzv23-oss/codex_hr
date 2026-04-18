<?php

namespace App\Models;

use CodeIgniter\Model;

class QrPoint extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'qr_points';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'name', 'location', 'description', 'token', 'is_active', 'created_at', 'updated_at'];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
}
