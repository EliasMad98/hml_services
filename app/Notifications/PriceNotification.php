<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class PriceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $price ;
    public $complaint;
    public $invoiceURL;

    public function __construct($price , $complaint,$invoiceURL)
    {
        $this->price=$price;
        $this->complaint=$complaint;
        $this->invoiceURL=$invoiceURL;
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
    public function toFirebase($notifiable)
    {
        // $deviceTokens = [
        //     '{TOKEN_1}',
        //     '{TOKEN_2}'
        // ];

        return (new FirebaseMessage)
            ->withTitle("Price Notification")
            ->withBody("We estimated the price of your repair as $this->price AED .")
            //->withImage('https://firebase.google.com/images/social.png')
            ->withIcon('https://seeklogo.com/images/F/firebase-logo-402F407EE0-seeklogo.com.png')
            ->withSound('default')
            // ->withClickAction('https://www.google.com')
            ->withPriority('high')
            ->withAdditionalData([
                'badge' => 0,
                'nav_type' =>  'price',
                'complaint_id' => $this->complaint->id ,
                'price' => $this->price ,
                'invoiceURL' =>$this->invoiceURL

            ])
            ->asNotification([$notifiable->fcm_token]); // OR ->asMessage($deviceTokens);
            // ->sendNotification($notifiable->fcm_token);
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
            'title'=>'Price Notification',
            'message'=>"We estimated the price of your repair as $this->price AED .
              if you want to proceed click here to continue  ",
            'url'=>''
        ];
    }
}
