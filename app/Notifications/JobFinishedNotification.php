<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;


class JobFinishedNotification extends Notification
{
    use Queueable;

    public $complaint_id;

    public function __construct($complaint_id)
    {

        $this->complaint_id=$complaint_id;
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
            ->withTitle("Job Finished Notification")
            ->withBody("The Repairman has finished the complaint")
            ->withSound('default')
            ->withPriority('high')
            ->withAdditionalData([
                'finished' => 1,
                'nav_type'=>"finished_complaint",
                'complaint_id'=>$this->complaint_id
            ])
            ->asNotification([$notifiable->fcm_token]); // OR ->asMessage($deviceTokens);

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
            'title'=>'Job Finished Notification',
            'message'=>"The Repairman has finished the complaint",
        ];
    }
}
