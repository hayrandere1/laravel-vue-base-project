<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class AdminPasswordSendNotification extends Notification
{
    use Queueable;

    public $username;
    public $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get('app.notification.adminPasswordSend.mail.subject'))
            ->line(Lang::get('app.notification.adminPasswordSend.mail.text.username').' : '.$this->username)
            ->line(Lang::get('app.notification.adminPasswordSend.mail.text.password').' : '.$this->password)
            ->action(Lang::get("app.notification.adminPasswordSend.mail.action"), url('/Admin'))
            ->line(Lang::get('app.notification.adminPasswordSend.mail.text.thanks'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
