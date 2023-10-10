<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Group extends Model implements Auditable
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
    public function people():HasMany
    {
        return $this->hasMany(Person::class);
    }
}
