<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserMessageNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $message;
    public $complaintMessage;
     public $user;
    public function __construct($message,$complaintMessage , $user )
    {
        //
        $this->message= $message;
           $this->complaintMessage= $complaintMessage;
             $this->user= $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
     * Get the firebase representation of the notification.
     */
    // public function toFirebase($notifiable)
    // {
    //     // $deviceTokens = [
    //     //     '{TOKEN_1}',
    //     //     '{TOKEN_2}'
    //     // ];

    //     return (new FirebaseMessage)
    //         ->withTitle("New Message Notification")
    //         ->withBody("You have a new Message from HML app . $this->message ")
    //         //->withImage('https://firebase.google.com/images/social.png')
    //         ->withIcon('https://seeklogo.com/images/F/firebase-logo-402F407EE0-seeklogo.com.png')
    //         ->withSound('default')
    //         // ->withClickAction('https://www.google.com')
    //         ->withPriority('high')
    //         ->withAdditionalData([
    //             'color' => '#rrggbb',
    //             'badge' => 0,
    //         ])
    //         ->asNotification([$notifiable->fcm_token]); // OR ->asMessage($deviceTokens);
    //         // ->sendNotification($notifiable->fcm_token);
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $first_name=$this->user->first_name;
        $last_name=$this->user->last_name;
        return [
            'title'=>'New Message Notification',
            'message'=>"You have a new Message from $first_name  $last_name",
            'complaint_id'=> $this->complaintMessage->complaint_id ,
        ];
    }

}