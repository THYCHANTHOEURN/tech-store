<?php

namespace App\Notifications;

use App\Models\MessageThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $thread;
    protected $isReply;

    /**
     * Create a new notification instance.
     *
     * @param MessageThread $thread
     * @param bool $isReply
     */
    public function __construct(MessageThread $thread, bool $isReply = false)
    {
        $this->thread   = $thread;
        $this->isReply  = $isReply;
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
        $url = route('dashboard.messages.show', $this->thread->uuid);
        $subject = $this->isReply
            ? "Customer Reply: {$this->thread->subject}"
            : "New Customer Message: {$this->thread->subject}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello!')
            ->line($this->isReply
                ? "A customer has replied to a message thread."
                : "A new message has been received from a customer.")
            ->line("Subject: {$this->thread->subject}")
            ->line("From: {$this->thread->user->name} ({$this->thread->user->email})")
            ->action('View Message', $url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'thread_id'         => $this->thread->id,
            'thread_uuid'       => $this->thread->uuid,
            'subject'           => $this->thread->subject,
            'customer_name'     => $this->thread->user->name,
            'customer_email'    => $this->thread->user->email,
            'is_reply'          => $this->isReply,
        ];
    }
}
