<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Company extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
        'supervisor_id',
        'due_date',
    ];
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function managers()
    {
        return $this->hasMany(Manager::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function supervisor()
    {
        return $this->hasMany(Manager::class)->find($this->supervisor_id);
    }
    public function mainUser()
    {
        return $this->hasMany(User::class)->find($this->main_user_id);
    }

    public function managerRoleGroups()
    {
        return $this->hasMany(ManagerRoleGroup::class);
    }
    public function userRoleGroups()
    {
        return $this->hasMany(UserRoleGroup::class);
    }
    public function people()
    {
        return $this->hasManyThrough(Person::class, Group::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
