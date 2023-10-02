<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_role_group_id',
        'user_role_group_id',
        'is_active',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
    public function userRoleGroup()
    {
        return $this->belongsTo(UserRoleGroup::class);
    }
    public function managerRoleGroup()
    {
        return $this->belongsTo(ManagerRoleGroup::class);
    }


}
