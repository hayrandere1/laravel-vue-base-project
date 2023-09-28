<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'user_id');
    }
}
