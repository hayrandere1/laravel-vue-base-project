<?php

namespace App\Models;

use App\Notifications\ManagerPasswordSendNotification;
use App\Notifications\ManagerResetPasswordNotification;
use App\Notifications\ManagerVerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Manager extends Authenticatable implements MustVerifyEmail, Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable;

    /**
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role_group_id',
        'username',
        'is_active',
        'login_code',
        'login_code_expired'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param $username
     * @param $password
     * @return void
     */
    public function sendPasswordNotification($username, $password): void
    {
        $this->notify(new ManagerPasswordSendNotification($username, $password));
    }

    /**
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ManagerResetPasswordNotification($token, $this->username));
    }

    /**
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * @param $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'manager.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.managers.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * @return void
     */
    public function sendEmailVerificationNotification():void
    {
        $this->notify(new ManagerVerifyEmailNotification());
    }

    /**
     * @return BelongsTo
     */
    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function roleGroup():BelongsTo
    {
        return $this->belongsTo('App\Models\ManagerRoleGroup');
    }

    /**
     * @return HasMany
     */
    public function archives():HasMany
    {
        return $this->hasMany(Archive::class, 'user_id')->where('type', 'manager');
    }

    /**
     * @return HasMany
     */
    public function notifications():HasMany
    {
        return $this->hasMany(Notification::class, 'user_id')->where('type', 'manager');
    }
}
