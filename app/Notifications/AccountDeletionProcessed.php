<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\AccountDeletionRequest;

class AccountDeletionProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    public $deletionRequest;

    public function __construct(AccountDeletionRequest $deletionRequest)
    {
        $this->deletionRequest = $deletionRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $status = ucfirst($this->deletionRequest->status);

        $mail = (new MailMessage)
            ->subject("Your Account Deletion Request has been {$status}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your account deletion request has been **{$status}** by the admin.");

        if ($this->deletionRequest->status === 'approved') {
            $mail->line('Your account has now been deactivated as per your request.');
        } else {
            $mail->line('Your account remains active.');
        }

        if ($this->deletionRequest->admin_note) {
            $mail->line('Admin Note: ' . $this->deletionRequest->admin_note);
        }

        $mail->line('Thank you for using our platform.');

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Account Deletion Request ' . ucfirst($this->deletionRequest->status),
            'message' => "Your deletion request was {$this->deletionRequest->status}.",
            'admin_note' => $this->deletionRequest->admin_note,
        ];
    }
}
