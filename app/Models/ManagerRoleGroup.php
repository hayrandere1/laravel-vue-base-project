<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ManagerRoleGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $fillable = ['name'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\ManagerRole', 'manager_permissions', 'role_group_id', 'role_id')->withPivot('filter_type', 'filter_values');
    }

}
