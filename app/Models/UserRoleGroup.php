<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class UserRoleGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @return BelongsTo
     */
    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany
     */
    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function roles():BelongsToMany
    {
        return $this->belongsToMany('App\Models\UserRole', 'user_permissions', 'role_group_id', 'role_id')->withPivot('filter_type', 'filter_values');
    }
}
