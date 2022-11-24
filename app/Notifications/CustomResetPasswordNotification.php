<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class CustomResetPasswordNotification extends ResetPassword
{

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('email.reset_password_notification'))
            ->line(trans('email.reset_password_t1'))
            ->action(trans('email.reset_password'), url(config('app.url').route('password.reset', ['token' => $this->token, 'username' => $notifiable->getEmailForPasswordReset()], false)))
            ->line(trans('email.reset_password_t2', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(trans('email.reset_password_t3'));
    }

}
