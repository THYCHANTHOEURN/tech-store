<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail']; // Store in database and send email
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('dashboard.orders.show', $this->order->uuid);

        return (new MailMessage)
            ->subject('New Order Received - #' . strtoupper(substr($this->order->uuid, -8)))
            ->greeting('Hello!')
            ->line('A new order has been placed.')
            ->line('Order #: ' . strtoupper(substr($this->order->uuid, -8)))
            ->line('Total: $' . number_format($this->order->total_amount, 2))
            ->line('Customer: ' . $this->order->user->name)
            ->action('View Order', $url)
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
            'order_id'      => $this->order->id,
            'order_uuid'    => $this->order->uuid,
            'total_amount'  => $this->order->total_amount,
            'customer_name' => $this->order->user->name,
            'order_status'  => $this->order->status,
        ];
    }
}
