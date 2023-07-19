<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class UserSendMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $conversationId;
    private $adTitle;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($conversationId, $adTitle)
    {
        $this->conversationId = $conversationId;
        $this->adTitle        = $adTitle;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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

    public function toFcm($notifiable)
    {
       
        return FcmMessage::create()
            ->setData(['conversation_id' => (string)$this->conversationId])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle(''. $this->adTitle . '')
                ->setBody('You have a new message.'));
        
    }
}
