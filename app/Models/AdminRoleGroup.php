<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AdminRoleGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $fillable = ['name'];

    public function admins()
    {
        return $this->hasMany('App\Models\Admin');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\AdminRole', 'admin_permissions', 'role_group_id', 'role_id')->withPivot('filter_type','filter_values');
    }
}
