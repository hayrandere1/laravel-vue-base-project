<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class AdminRole extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'parent',
        'route_name',
        'controller',
        'action',
        'model',
        'is_active'];

    /**
     * @return BelongsToMany
     */
    public function roleGroups():BelongsToMany
    {
        return $this->belongsToMany('App\Models\AdminRoleGroup', 'permissions', 'role_id', 'role_group_id');
    }

    /**
     * @return Model|BelongsTo|object|null
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\AdminRole')->first();
    }

    /**
     * @return HasMany
     */
    public function children():HasMany
    {
        return $this->hasMany('App\Models\AdminRole', 'parent', 'id');
    }
}
