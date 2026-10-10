<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ImplementasiFollowUpNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $implementasiId;
    protected $title;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param int $implementasiId
     */
    public function __construct($title, $message, $implementasiId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->implementasiId = $implementasiId;
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
            'implementasi_id' => $this->implementasiId,
        ];
    }
}
