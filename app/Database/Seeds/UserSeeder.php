<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = env('seeder.adminPassword');
        if (empty($password)) {
            if (ENVIRONMENT !== 'development') {
                return;
            }
            /* admin123*/
            $password = bin2hex(random_bytes(8));
            CLI::write('Seeder admin temporal creado para desarrollo. Password: ' . $password, 'yellow');
        }

        $data = [
            'name'       => 'Administrador General',
            'email'      => 'admin@codexhr.com',
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Ensure no duplicate using ignore pattern or pre-checking
        if ($this->db->table('users')->where('email', 'admin@codexhr.com')->countAllResults() === 0) {
            $this->db->table('users')->insert($data);
        }
    }
}
