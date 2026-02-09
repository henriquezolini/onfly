<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestStatusChanged extends Notification
{
    use Queueable;

    protected $travelRequest;

    public function __construct(TravelRequest $travelRequest)
    {
        $this->travelRequest = $travelRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The status of your travel request has changed.')
                    ->action('View Request', url('/travel-requests/' . $this->travelRequest->id))
                    ->line('Current status: ' . $this->travelRequest->status);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->travelRequest->id,
            'status' => $this->travelRequest->status,
            'message' => "Your travel request to {$this->travelRequest->destination} has been {$this->travelRequest->status}.",
        ];
    }
}