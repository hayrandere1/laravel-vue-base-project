<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ManagerRole extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $fillable = ['parent', 'route_name', 'controller', 'action', 'model', 'is_active'];

    public function roleGroups()
    {
        return $this->belongsToMany('App\Models\ManagerRoleGroup', 'permissions', 'role_id', 'role_group_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\ManagerRole')->first();
    }

    public function children()
    {
        return $this->hasMany('App\Models\ManagerRole', 'parent', 'id');
    }
}
