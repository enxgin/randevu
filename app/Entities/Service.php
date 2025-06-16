<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Service extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'is_active' => 'boolean',
        'price' => 'float',
        'duration_minutes' => 'integer',
        'category_id' => 'integer',
        'branch_id' => '?integer', // Nullable integer
        'requires_special_equipment' => '?boolean' // Nullable boolean
    ];

    // branch_id null olabileceğinden, ona göre bir cast ekleyebiliriz veya getter/setter ile yönetebiliriz.
    // Şimdilik ?integer ile nullable olduğunu belirttik.
}