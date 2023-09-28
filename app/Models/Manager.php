<?php

namespace App\Models;

use App\Notifications\ManagerPasswordSendNotification;
use App\Notifications\ManagerResetPasswordNotification;
use App\Notifications\ManagerVerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Manager extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the password notification.
     *
     * @param string $password
     * @return void
     */
    public function sendPasswordNotification($username, $password)
    {
        $this->notify(new ManagerPasswordSendNotification($username, $password));
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ManagerResetPasswordNotification($token, $this->username));
    }

    /**
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'user.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.managers.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new ManagerVerifyEmailNotification());
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function roleGroup()
    {
        return $this->belongsTo('App\Models\ManagerRoleGroup');
    }

    public function archives()
    {
        return $this->hasMany(Archive::class,'user_id')->where('type', 'manager');
    }
}
