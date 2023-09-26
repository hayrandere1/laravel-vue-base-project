<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Notification extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'content', 'link', 'is_read', 'type'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin','user_id');
    }

    public function getStatusAttribute()
    {
        //@Todo: user type a göre şekillenmeli
        return ($this->is_read) ? __('admin.notification.statuses.read') : __('admin.notification.statuses.unread');
    }

}
