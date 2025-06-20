<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherAccountActivated extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $teacher;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $teacher)
    {
        $this->teacher = $teacher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your Teacher Account has been Activated')
                    ->greeting('Hello ' . $this->teacher->name . ',')
                    ->line('Great news! Your teacher account on our platform has been activated by an administrator.')
                    ->line('You can now log in and access all the features available to teachers, including creating flashcard sets and assigning them to students.')
                    ->action('Go to Dashboard', url('/dashboard'))
                    ->line('Thank you for joining our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
