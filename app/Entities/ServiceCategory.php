<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ServiceCategory extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'is_active' => 'boolean',
        'branch_id' => '?integer' // Nullable integer
    ];
}