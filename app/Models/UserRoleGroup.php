<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserRoleGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $fillable = ['name'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\UserRole', 'user_permissions', 'role_group_id', 'role_id')->withPivot('filter_type', 'filter_values');
    }
}
