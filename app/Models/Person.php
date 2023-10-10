<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Person extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'group_id',
        'phone',
        'email'
    ];

    /**
     * @return BelongsTo
     */
    public function group():BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
