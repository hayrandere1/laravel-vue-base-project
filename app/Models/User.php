<?php

namespace App\Models;

use App\Notifications\UserPasswordSendNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserResetPasswordNotification;
use App\Notifications\UserVerifyEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Client;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Send the password notification.
     *
     * @param  string  $password
     * @return void
     */
    public function sendPasswordNotification($username, $password)
    {
        $this->notify(new UserPasswordSendNotification($username, $password));
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function apiClient()
    {
        return $this->hasOne(Client::class);
    }

    /**
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token, $this->username));
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'user.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.users.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new UserVerifyEmailNotification());
    }

    public function hasVerifiedEmail()
    {
        if (!is_null($this->role_group_id)) {
            return true;
        }
        return !is_null($this->email_verified_at);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function roleGroup()
    {
        return $this->belongsTo(UserRoleGroup::class);
    }
    public function archives()
    {
        return $this->hasMany(Archive::class)->where('type', 'user');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->where('type', 'user');
    }
}
