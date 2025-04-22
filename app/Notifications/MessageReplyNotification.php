<?php

namespace App\Notifications;

use App\Models\MessageThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $thread;

    /**
     * Create a new notification instance.
     *
     * @param MessageThread $thread
     */
    public function __construct(MessageThread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('messages.show', $this->thread->uuid);

        return (new MailMessage)
            ->subject("New Reply: {$this->thread->subject}")
            ->greeting('Hello!')
            ->line("You have received a reply to your message.")
            ->line("Subject: {$this->thread->subject}")
            ->action('View Message', $url)
            ->line('Thank you for contacting us!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'thread_id'     => $this->thread->id,
            'thread_uuid'   => $this->thread->uuid,
            'subject'       => $this->thread->subject,
        ];
    }
}
