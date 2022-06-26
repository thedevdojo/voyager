<?php

namespace TCG\Voyager\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('voyager::passwords.reset_notification_subject'))
            ->line(__('voyager::passwords.reset_notification_first_line'))
            ->greeting(__('abeceder::passwords.reset_greeting', ['firstname' => $notifiable->firstname]))
            ->action(__('voyager::passwords.reset_password'), url(config('app.url') . route('voyager.password.reset', [
                    $this->token,
                    encrypt($notifiable->email)
                ], false)))
            ->line(__('voyager::passwords.reset_notification_second_line'));
    }
}
