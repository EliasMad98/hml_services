<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;


class UpdatePriceNotification extends Notification
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

    public function __construct($price , $complaint , $invoiceURL=null)
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
     * Get the firebase representation of the notification.
     */
    public function toFirebase($notifiable)
    {

        return (new FirebaseMessage)
            ->withTitle("Update Price Notification")
            ->withBody("We update the price of your repair as $this->price AED .")
            //->withImage('hattps://firebase.google.com/images/social.png')
            ->withIcon('https://seeklogo.com/images/F/firebase-logo-402F407EE0-seeklogo.com.png')
            ->withSound('default')
            // ->withClickAction('https://www.google.com')
            ->withPriority('high')
            ->withAdditionalData([
                'badge' => 0,
                'nav_type' =>  'update_price',
                'complaint_id' => $this->complaint->id ,
                'price' => $this->price ,
                'invoiceURL' =>$this->invoiceURL ?? null

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
            'title'=>'Update Price Notification',
            'message'=>"We update the price of your repair as $this->price AED .",
        ];
    }
}
