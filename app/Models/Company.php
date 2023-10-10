<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Company extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'is_active',
        'package_id',
        'supervisor_id',
        'main_user_id',
        'due_date',
    ];
    /**
     * @var string[]
     */
    protected $casts = [
        'due_date' => 'datetime:Y-m-d',
    ];

    /**
     * @return BelongsTo
     */
    public function package():BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return HasMany
     */
    public function managers():HasMany
    {
        return $this->hasMany(Manager::class);
    }

    /**
     * @return HasMany
     */
    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany|mixed
     */
    public function supervisor()
    {
        return $this->hasMany(Manager::class)->find($this->supervisor_id);
    }

    /**
     * @return HasMany|mixed
     */
    public function mainUser()
    {
        return $this->hasMany(User::class)->find($this->main_user_id);
    }

    /**
     * @return HasMany
     */
    public function managerRoleGroups():HasMany
    {
        return $this->hasMany(ManagerRoleGroup::class);
    }

    /**
     * @return HasMany
     */
    public function userRoleGroups():HasMany
    {
        return $this->hasMany(UserRoleGroup::class);
    }

    /**
     * @return HasManyThrough
     */
    public function people():HasManyThrough
    {
        return $this->hasManyThrough(Person::class, Group::class);
    }

    /**
     * @return HasMany
     */
    public function groups():HasMany
    {
        return $this->hasMany(Group::class);
    }
}
