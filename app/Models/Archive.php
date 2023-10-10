<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archive extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'sql',
        'parameters',
        'status',
        'file_name',
        'total_count',
        'completed_count',
        'columns',
        'unique_id',
        'unique_file_name_id',
        'language',
        'type'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo('App\Models\Manager', 'user_id');
    }
}
