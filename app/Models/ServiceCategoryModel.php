<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceCategoryModel extends Model
{
    protected $table            = 'service_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Opsiyonel: Yumuşak silme kullanacaksanız

    protected $allowedFields    = ['branch_id', 'name', 'description', 'color', 'sort_order', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // useSoftDeletes true ise gereklidir

    // Validation
    protected $validationRules      = [
        'name' => 'required|string|max_length[255]',
        'branch_id' => 'permit_empty|if_exist|is_natural_no_zero',
        'description' => 'permit_empty|string',
        'color' => 'permit_empty|string|max_length[7]',
        'sort_order' => 'permit_empty|integer',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // İlişkiler
    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

    public function services()
    {
        return $this->hasMany(ServiceModel::class, 'category_id');
    }
}