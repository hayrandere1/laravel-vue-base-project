<?php

namespace App\Models;

use App\Notifications\UserPasswordSendNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserResetPasswordNotification;
use App\Notifications\UserVerifyEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Client;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;


class User extends Authenticatable implements MustVerifyEmail, Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, HasFactory, Notifiable;

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
        'password' => 'hashed',
    ];

    /**
     * @param $username
     * @param $password
     * @return void
     */
    public function sendPasswordNotification($username, $password): void
    {
        $this->notify(new UserPasswordSendNotification($username, $password));
    }


    /**
     * @return HasOne
     */
    public function apiClient(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
    /**
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new UserResetPasswordNotification($token, $this->username));
    }

    /**
     * @param $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable): string
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

    /**
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new UserVerifyEmailNotification());
    }

    /**
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        if (!is_null($this->role_group_id)) {
            return true;
        }
        return !is_null($this->email_verified_at);
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function roleGroup(): BelongsTo
    {
        return $this->belongsTo(UserRoleGroup::class);
    }

    /**
     * @return HasMany
     */
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class)->where('type', 'user');
    }

    /**
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('type', 'user');
    }
}
