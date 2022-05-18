<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\Twilio;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SmsVerifyNotification extends Notification
{
    use Queueable;
    private string $title;
    private string $code;

    /**
     * SmsVerifyNotification constructor.
     * @param string|int $code
     * @param string|null $title
     */
    public function __construct(string|int $code,$title)
    {
        $this->title = $title ?? "Dispatcher Verify Code : ";
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }


    public function toTwilio(){
        return (new TwilioSmsMessage())
                ->content($this->title.$this->code);
    }

}
