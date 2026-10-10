<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketDocumentNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $ticketId;
    protected $title;
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param string $ticketId
     * @param string $url
     */
    public function __construct($title, $message, $ticketId, $url)
    {
        $this->title = $title;
        $this->message = $message;
        $this->ticketId = $ticketId;
        $this->url = $url;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'ticket_id' => $this->ticketId,
            'url' => $this->url,
        ];
    }
}
