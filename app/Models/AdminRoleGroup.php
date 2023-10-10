<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class AdminRoleGroup extends Model implements Auditable
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
     * @return HasMany
     */
    public function admins():HasMany
    {
        return $this->hasMany('App\Models\Admin');
    }

    /**
     * @return BelongsToMany
     */
    public function roles():BelongsToMany
    {
        return $this->belongsToMany('App\Models\AdminRole', 'admin_permissions', 'role_group_id', 'role_id')->withPivot('filter_type','filter_values');
    }
}
