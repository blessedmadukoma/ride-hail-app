<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class LoginNeedsVerification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [TwilioChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toTwilio(object $notifiable)
    {
        // generate code
        $loginCode = rand(111111, 999999);

        // save the state of the code
        $notifiable->update([
            'login_code' => $loginCode
        ]);

        // send the code to the user
        return (new TwilioSmsMessage())
            ->content("Your Ridaar login code is: {$loginCode}, do not share this with any one.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
