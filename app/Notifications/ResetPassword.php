<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends \Illuminate\Auth\Notifications\ResetPassword
{

    public function toMail($notifiable)
    {
        $url =url( config('app.client_url').'/password/reset/'.$this->token ).'?email='.urlencode($notifiable->email);
        return (new MailMessage)
            ->line( 'The introduction to the notification.' )
            ->action( 'Reset Password', $url )
            ->line( 'Thank you for using our application!' );
    }
}
