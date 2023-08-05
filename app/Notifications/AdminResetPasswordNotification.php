<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class AdminResetPasswordNotification extends ResetPassword
{

    private $username;
    public function __construct($token, $username)
    {
        $this->username = $username;
        parent::__construct($token);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }
        if (static::$createUrlCallback) {
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }else{
            $url = url(route('admin.password.reset', [
                'token' => $this->token,
                'username' => $this->username,
            ], false));
        }

        return (new MailMessage)
            ->subject(Lang::get('app.notification.adminResetPassword.mail.subject'))
            ->line(Lang::get('app.notification.adminResetPassword.mail.text.youAreReceiving'))
            ->action(Lang::get('app.notification.adminResetPassword.mail.action'), $url)
            ->line(Lang::get('app.notification.adminResetPassword.mail.text.expireIn', [
                'count' => config('auth.passwords.admins.expire'
                )]))
            ->line(Lang::get('app.notification.adminResetPassword.mail.text.ifYouDidNotRequest'));

    }

}
