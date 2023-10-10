<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Package extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'manager_role_group_id',
        'user_role_group_id',
        'is_active',
    ];

    /**
     * @return HasMany
     */
    public function companies():HasMany
    {
        return $this->hasMany(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function userRoleGroup():BelongsTo
    {
        return $this->belongsTo(UserRoleGroup::class);
    }

    /**
     * @return BelongsTo
     */
    public function managerRoleGroup():BelongsTo
    {
        return $this->belongsTo(ManagerRoleGroup::class);
    }


}
