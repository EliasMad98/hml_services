<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;


class VisitNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    // public $price ;
    public $complaint;

    public function __construct($complaint)
    {
        // $this->price=$price;
        $this->complaint=$complaint;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','firebase'];

    }

    /**
     * Get the firebase representation of the notification.
     */
    public function toFirebase($notifiable)
    {
        // $deviceTokens = [
        //     '{TOKEN_1}',
        //     '{TOKEN_2}'
        // ];

        return (new FirebaseMessage)
            ->withTitle("Visit Notification")
            ->withBody("We need you to choose a date for a visit to determine the repair cost")
            //->withImage('https://firebase.google.com/images/social.png')
            ->withIcon('https://seeklogo.com/images/F/firebase-logo-402F407EE0-seeklogo.com.png')
            ->withSound('default')
            // ->withClickAction('https://www.google.com')
            ->withPriority('high')
            ->withAdditionalData([
                'color' => '#rrggbb',
                'badge' => 0,
                'complaint_id' => $this->complaint->id ,
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
            'title'=>'Visit Notification',
            'message'=>"We need you to choose a date for a visit to determine the repair cost",
        ];
    }
}
