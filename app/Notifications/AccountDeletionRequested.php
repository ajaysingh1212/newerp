<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\AccountDeletionRequest;

class AccountDeletionRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public $deletionRequest;

    public function __construct(AccountDeletionRequest $deletionRequest)
    {
        $this->deletionRequest = $deletionRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // mail + notification bell (optional)
    }

    public function toMail($notifiable)
    {
        $user = $this->deletionRequest->user;

        return (new MailMessage)
            ->subject('New Account Deletion Request')
            ->greeting('Hello Admin,')
            ->line("User **{$user->name}** ({$user->email}) has requested account deletion.")
            ->line('Reason: ' . ($this->deletionRequest->reason ?: 'No reason provided.'))
            ->action('View Request', url('/admin/deletion-requests/' . $this->deletionRequest->id))
            ->line('Please review and approve or reject the request.');
    }

    public function toArray($notifiable)
    {
        $user = $this->deletionRequest->user;
        return [
            'title' => 'New account deletion request',
            'message' => "{$user->name} ({$user->email}) requested account deletion.",
            'request_id' => $this->deletionRequest->id,
        ];
    }
}
