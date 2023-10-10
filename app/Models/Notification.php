<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Notification extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'link',
        'is_read',
        'type'
    ];

    /**
     * @return BelongsTo
     */
    public function user():BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return BelongsTo
     */
    public function manager():BelongsTo
    {
        return $this->belongsTo('App\Models\Manager','user_id');
    }

    /**
     * @return BelongsTo
     */
    public function admin():BelongsTo
    {
        return $this->belongsTo('App\Models\Admin','user_id');
    }

}
