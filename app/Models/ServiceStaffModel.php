<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceStaffModel extends Model
{
    protected $table            = 'service_staff';
    protected $primaryKey       = 'id'; // Genellikle pivot tablolarda ayrı bir id olur, yoksa composite key kullanılır.
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Veya App\Entities\ServiceStaff
    protected $useSoftDeletes   = false; // Pivot tablolarda genellikle soft delete kullanılmaz

    // Hangi hizmetin hangi personel tarafından verilebileceğini belirten alanlar
    protected $allowedFields    = ['service_id', 'staff_id', 'user_id', 'branch_id']; // staff_id eklendi

    // Dates - Pivot tablolarda genelde timestamp tutulmaz, gerekirse eklenebilir.
    protected $useTimestamps = false; // Tablo yapısında updated_at yok
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'service_id' => 'required|is_natural_no_zero|is_not_unique[services.id]',
        'user_id'    => 'required|is_natural_no_zero|is_not_unique[users.id]',
        'staff_id'   => 'permit_empty|is_natural_no_zero|is_not_unique[users.id]'
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

    // İlişkiler (Pivot modelde genelde tanımlanmaz ama gerekirse)
    public function service()
    {
        return $this->belongsTo(ServiceModel::class, 'service_id');
    }

    public function user() // Personel
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }
}