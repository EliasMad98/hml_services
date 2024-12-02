<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class CustomNotification extends Notification
{
    use Queueable;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    // public $deviceTokens ;
    public $title ;
    public $body ;
    public $image ;
    public $icon ;
    public $sound;
    public $clickAction ;
    public $priority ;


    public function __construct(//$deviceTokens,
        $title,$body,$image,$icon,$clickAction,$sound="default",$priority="high")
    {
        // $this->deviceTokens=$deviceTokens;
        $this->title=$title;
        $this->body=$body;
        $this->image=$image;
        $this->icon=$icon;
        $this->clickAction=$clickAction;
        $this->sound=$sound;
        $this->priority=$priority;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }
    public function toFirebase($notifiable)
    {
        // $deviceTokens = [
        //     '{TOKEN_1}',
        //     '{TOKEN_2}'
        // ];

        return (new FirebaseMessage)
            ->withTitle($this->title)
            ->withBody($this->body)
            // ->withImage($this->image)
            // ->withIcon($this->icon)
            ->withSound($this->sound)
            ->withClickAction($this->clickAction)
            ->withPriority($this->priority)
            ->withAdditionalData([
                'color' => '#rrggbb',
                'badge' => 0,
            ])
            ->asNotification([$notifiable->fcm_token]); // OR ->asMessage($deviceTokens);
            // ->sendNotification($notifiable->fcm_token);
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
}
