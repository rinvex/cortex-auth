<?php

declare(strict_types=1);

namespace Cortex\Fort\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdminEmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The email verification token.
     *
     * @var string
     */
    public $token;

    /**
     * The email verification expiration date.
     *
     * @var int
     */
    public $expiration;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @param string $expiration
     */
    public function __construct($token, $expiration)
    {
        $this->token = $token;
        $this->expiration = $expiration;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $email = $notifiable->getEmailForVerification();
        $link = route('adminarea.verification.email.verify')."?email={$email}&expiration={$this->expiration}&token={$this->token}";

        return (new MailMessage())
            ->subject(trans('cortex/fort::emails.verification.email.subject'))
            ->line(trans('cortex/fort::emails.verification.email.intro', ['expire' => now()->createFromTimestamp($this->expiration)->diffForHumans()]))
            ->action(trans('cortex/fort::emails.verification.email.action'), $link)
            ->line(trans('cortex/fort::emails.verification.email.outro'));
    }
}