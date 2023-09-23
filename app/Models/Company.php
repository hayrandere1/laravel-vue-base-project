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
    public function managers()
    {
        return $this->hasMany(Manager::class);
    }
    public function supervisor()
    {
        return $this->hasMany(Manager::class)->find($this->supervisor_id);
    }

    public function managerRoleGroups()
    {
        return $this->hasMany(ManagerRoleGroup::class);
    }
}
